<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\UserSettings;

class UserSettingsController extends Controller
{
    /**
     * Update account: username, email, password, profile picture.
     */
    public function updateAccount(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
        if ($request->hasFile('profile_picture')) {
            $rules['profile_picture'] = ['image', 'mimes:jpeg,png,gif,webp', 'max:2048'];
        }

        $valid = $request->validate($rules);

        $oldUsername = $user->username;
        $oldEmail = $user->email;
        $passwordChanged = !empty($valid['password']);

        $user->username = $valid['username'];
        $user->email = $valid['email'];
        if ($passwordChanged) {
            $user->password = Hash::make($valid['password']);
        }

        if ($request->hasFile('profile_picture')) {
            $dir = 'profile-pictures';
            $file = $request->file('profile_picture');
            $name = 'user-' . $user->id . '-' . time() . '.jpg';
            $path = $dir . '/' . $name;
            $encodedImage = \Intervention\Image\Laravel\Facades\Image::decode($file)->scaleDown(width: 800)->encodeUsingFileExtension('jpg', quality: 80);
            
            $disk = config('filesystems.media_disk');
            \Illuminate\Support\Facades\Storage::disk($disk)->put($path, (string) $encodedImage);
            
            $oldFile = $user->profile_picture;
            $user->profile_picture = $path;
            try {
                if ($oldFile) {
                    \Illuminate\Support\Facades\Storage::disk($disk)->delete('profile-pictures/' . basename($oldFile));
                }
            } catch (\Throwable $e) {}
            
            $user->save();
        } else {
            $user->save();
        }

        $changes = [];
        if ($oldUsername !== $user->username) {
            $changes[] = 'Username updated from "' . \App\Helpers\ChangeLogHelper::maskMiddle($oldUsername) . '" to "' . \App\Helpers\ChangeLogHelper::maskMiddle($user->username) . '"';
        }
        if ($oldEmail !== $user->email) {
            $changes[] = 'Email updated from "' . \App\Helpers\ChangeLogHelper::maskEmail($oldEmail) . '" to "' . \App\Helpers\ChangeLogHelper::maskEmail($user->email) . '"';
        }
        if ($passwordChanged) {
            $changes[] = 'Password updated (from "********" to "' . \App\Helpers\ChangeLogHelper::maskMiddle($valid['password']) . '")';
        }

        if (!empty($changes)) {
            \App\Models\UserNotification::create([
                'user_id' => $user->id,
                'type' => \App\Models\UserNotification::TYPE_INFO,
                'title' => 'Security settings updated',
                'message' => implode("\n", $changes),
            ]);

            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\AccountUpdatedMail($user, $changes));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send account updated email: ' . $e->getMessage());
            }
        }

        return redirect()->route('user.settings.user.page')
            ->with('success', 'Account updated successfully.');
    }

    /**
     * Update general settings: notifications, alerts.
     */
    public function updateGeneral(Request $request)
    {
        $settings = UserSettings::firstOrCreate(['user_id' => Auth::id()]);
        
        $settings->page_view_alerts = $request->has('page_view_alerts');
        $settings->whatsapp_notifications = $request->has('whatsapp_notifications');
        
        $settings->save();

        return redirect()->route('user.settings.user.page')
            ->with('success', 'General preferences updated.');
    }

    /**
     * Update page settings: page_expiry (hours), auto_delete_expired.
     */
    public function updatePageSettings(Request $request)
    {
        $request->validate([
            'page_expiry'     => 'required|integer|min:1|max:8760',
            'auto_delete'     => 'required|in:immediately,1_hour,1_day,1_week,1_month,2_months,6_months,never',
        ]);

        $settings = UserSettings::firstOrCreate(
            ['user_id' => Auth::id()],
            ['page_expiry' => 24, 'page_view_alerts' => true, 'whatsapp_notifications' => true, 'auto_delete_expired' => 'never']
        );
        $settings->page_expiry = (int) $request->input('page_expiry');
        $settings->auto_delete_expired = $request->input('auto_delete');
        $settings->save();

        // Update expires_at for existing messages based on the new page_expiry
        $messages = \App\Models\WishMessages::where('user_id', Auth::id())
            ->whereNotNull('receiving_date')
            ->where('is_published', false) // assuming we only want to update active ones, wait, maybe just those that haven't expired yet? Or all? Let's just update all where expires_at > now or maybe we just update expiry_hours and recalculate expires_at for all messages for this user.
            ->get();
            
        foreach ($messages as $msg) {
            $msg->expiry_hours = $settings->page_expiry;
            $rd = $msg->receiving_date;
            if ($rd) {
                $msg->expires_at = \Carbon\Carbon::parse($rd instanceof \DateTimeInterface ? $rd : (string) $rd)->setTime(now()->hour, now()->minute, now()->second)->addHours((int) $msg->expiry_hours);
            } else {
                $msg->expires_at = now()->addHours((int) $msg->expiry_hours);
            }
            $msg->save();
        }

        return redirect()->route('user.settings.messages.page')
            ->with('success', 'Page settings saved and active messages updated.');
    }

    /**
     * Update notification preferences: page_view_alerts, whatsapp_notifications.
     */
    public function updateNotificationSettings(Request $request)
    {
        $settings = UserSettings::firstOrCreate(
            ['user_id' => Auth::id()],
            ['page_expiry' => 24, 'page_view_alerts' => true, 'whatsapp_notifications' => true, 'auto_delete_expired' => true]
        );
        $settings->page_view_alerts = $request->boolean('page_view_alerts');
        $settings->whatsapp_notifications = $request->boolean('whatsapp_notifications');
        $settings->save();

        return redirect()->route('user.settings.messages.page')
            ->with('success', 'Notification preferences saved.');
    }

    /**
     * Reset all user settings to defaults.
     */
    public function resetSettings(Request $request)
    {
        $settings = UserSettings::firstOrCreate(
            ['user_id' => Auth::id()],
            ['page_expiry' => 24, 'page_view_alerts' => true, 'whatsapp_notifications' => true, 'auto_delete_expired' => true]
        );
        $settings->page_expiry = 24;
        $settings->page_view_alerts = true;
        $settings->whatsapp_notifications = true;
        $settings->auto_delete_expired = true;
        $settings->save();

        return back()
            ->with('success', 'Settings reset to defaults.');
    }

    public function updatePrivacyAndTheme(Request $request)
    {
        $request->validate([
            'privacy_blur_enabled' => 'nullable|in:on,yes,1,true,0,false,off',
        ]);

        $settings = UserSettings::firstOrCreate(
            ['user_id' => Auth::id()],
            ['page_expiry' => 24, 'page_view_alerts' => true, 'whatsapp_notifications' => true, 'auto_delete_expired' => true]
        );
        
        $settings->privacy_blur_enabled = $request->boolean('privacy_blur_enabled');
        $settings->save();

        return back()
            ->with('success', 'Security & Appearance settings updated.');
    }

    public function updatePasscode(Request $request)
    {
        $request->validate([
            'login_passcode' => 'nullable|string|min:4|confirmed',
        ]);

        $settings = UserSettings::firstOrCreate(
            ['user_id' => Auth::id()],
            ['page_expiry' => 24, 'page_view_alerts' => true, 'whatsapp_notifications' => true, 'auto_delete_expired' => true]
        );
        
        $oldPasscodeExists = !empty($settings->login_passcode);
        $newPasscodeFilled = $request->filled('login_passcode');

        if ($newPasscodeFilled) {
            $settings->login_passcode = \Illuminate\Support\Facades\Hash::make($request->login_passcode);
            // Verify immediately so they don't get locked out right after setting it
            session(['passcode_verified' => true]);
        } else {
            $settings->login_passcode = null;
        }
        
        $settings->save();

        $changes = [];
        if ($oldPasscodeExists && !$newPasscodeFilled) {
            $changes[] = 'Login Passcode removed';
        } elseif ($newPasscodeFilled) {
            $changes[] = 'Login Passcode updated (from "' . ($oldPasscodeExists ? '****' : 'None') . '" to "' . \App\Helpers\ChangeLogHelper::maskMiddle($request->login_passcode) . '")';
        }

        if (!empty($changes)) {
            $user = Auth::user();
            \App\Models\UserNotification::create([
                'user_id' => $user->id,
                'type' => \App\Models\UserNotification::TYPE_INFO,
                'title' => 'Security settings updated',
                'message' => implode("\n", $changes),
            ]);

            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\AccountUpdatedMail($user, $changes));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send passcode updated email: ' . $e->getMessage());
            }
        }

        return back()
            ->with('success', 'Login Passcode updated successfully.');
    }


    /**
     * Download a JSON copy of all personal user data.
     */
    public function downloadData()
    {
        $user = Auth::user();
        
        $data = [
            'account' => $user->only(['name', 'username', 'email', 'created_at']),
            'settings' => $user->settings ? $user->settings->toArray() : null,
            'messages' => $user->wishMessages()->with('views')->get()->toArray(),
            'media_files' => $user->mediaFiles()->get()->toArray(),
        ];

        $fileName = 'wisp_data_' . $user->username . '_' . now()->format('Y_m_d_His') . '.csv';
        
        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, CSV_PRETTY_PRINT);
        }, $fileName, [
            'Content-Type' => 'application/csv',
        ]);
    }

    /**
     * API endpoint to get user settings.
     */
    public function showSettings(Request $request)
    {
        $settings = UserSettings::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['page_expiry' => 24, 'page_view_alerts' => true, 'whatsapp_notifications' => true, 'auto_delete_expired' => true]
        );

        return response()->json([
            'success' => true,
            'settings' => $settings
        ]);
    }

    public function vaultPinDisplay(Request $request)
    {
        $settings = UserSettings::where('user_id', $request->user()->id)->first();

        return response()->json([
            'success' => true,
            'maskedVaultPin' => $settings?->masked_vault_pin ?? '',
        ]);
    }

    /**
     * Verify a user's Vault PIN without exposing the stored value to the client.
     */
    public function verifyVaultPin(Request $request)
    {
        $request->validate(['pin' => 'required|string|min:4|max:6']);

        $settings = UserSettings::where('user_id', $request->user()->id)->first();
        if (!$settings || !$settings->vault_pin) {
            return response()->json([
                'success' => false,
                'message' => 'Vault PIN is not configured.',
            ], 422);
        }

        try {
            $storedPin = \Illuminate\Support\Facades\Crypt::decryptString($settings->vault_pin);
            $isCorrect = hash_equals($storedPin, $request->input('pin'));
        } catch (\Exception $e) {
            $isCorrect = Hash::check($request->input('pin'), $settings->vault_pin);
        }

        if (!$isCorrect) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect Vault PIN.',
            ], 422);
        }

        return response()->json(['success' => true]);
    }

    /**
     * API endpoint to update user settings.
     */
    public function updateSettings(Request $request)
    {
        $settings = UserSettings::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['page_expiry' => 24, 'page_view_alerts' => true, 'whatsapp_notifications' => true, 'auto_delete_expired' => true]
        );

        // Map React Native appLock fields to login_passcode
        if ($request->has('appLockEnabled')) {
            if (!$request->boolean('appLockEnabled')) {
                $settings->login_passcode = null;
            }
        }

        if ($request->filled('appLockPasscode')) {
            $settings->login_passcode = \Illuminate\Support\Facades\Hash::make($request->input('appLockPasscode'));
        }

        $vaultPin = $request->input('vault_pin', $request->input('globalVaultPin'));
        if ($vaultPin !== null && $vaultPin !== '') {
            $request->validate([
                'vault_pin' => 'nullable|string|min:4|max:6',
                'globalVaultPin' => 'nullable|string|min:4|max:6',
            ]);
            $settings->vault_pin = \Illuminate\Support\Facades\Crypt::encryptString($vaultPin);
        }

        // Fill any standard settings if they exist in the request
        $settings->fill($request->only([
            'page_expiry',
            'page_view_alerts',
            'whatsapp_notifications',
            'auto_delete_expired',
            'auto_archive_days',
            'privacy_blur_enabled',
            'theme_preference',
        ]));

        $settings->save();

        return response()->json([
            'success' => true,
            'settings' => $settings
        ]);
    }

    /**
     * Update all message settings and lifecycle rules at once.
     */
    public function updateAllMessageSettings(Request $request)
    {
        $vaultPinInput = $request->input('vault_pin');
        $hasStars = $vaultPinInput && str_contains($vaultPinInput, '*');

        $rules = [
            'page_expiry'       => 'required|integer|min:1|max:8760',
            'auto_delete'       => 'required|in:immediately,1_hour,1_day,1_week,1_month,2_months,6_months,never',
            'auto_archive_days' => 'nullable|integer|min:1',
        ];

        if ($request->filled('vault_pin') && !$hasStars) {
            $rules['vault_pin'] = 'required|string|min:4|max:6';
        }

        $request->validate($rules);

        $settings = UserSettings::firstOrCreate(['user_id' => Auth::id()]);

        // Notifications
        $settings->page_view_alerts = $request->boolean('page_view_alerts');
        $settings->whatsapp_notifications = $request->boolean('whatsapp_notifications');

        // Page Configuration
        $settings->page_expiry = (int) $request->input('page_expiry');
        $settings->auto_delete_expired = $request->input('auto_delete');

        // Vault PIN
        if ($request->filled('vault_pin') && !$hasStars) {
            $settings->vault_pin = \Illuminate\Support\Facades\Crypt::encryptString($request->input('vault_pin'));
        }

        // Auto Archive Days
        $settings->auto_archive_days = $request->input('auto_archive_days');

        $settings->save();

        // Update message expiry for user's wish messages based on new page_expiry
        $messages = \App\Models\WishMessages::where('user_id', Auth::id())
            ->whereNotNull('receiving_date')
            ->where('is_published', false)
            ->get();

        foreach ($messages as $msg) {
            $msg->expiry_hours = $settings->page_expiry;
            $rd = $msg->receiving_date;
            if ($rd) {
                $msg->expires_at = \Carbon\Carbon::parse($rd instanceof \DateTimeInterface ? $rd : (string) $rd)->setTime(now()->hour, now()->minute, now()->second)->addHours((int) $msg->expiry_hours);
            } else {
                $msg->expires_at = now()->addHours((int) $msg->expiry_hours);
            }
            $msg->save();
        }

        return redirect()->route('user.settings.messages.page')
            ->with('success', 'All message settings updated successfully.');
    }
}
