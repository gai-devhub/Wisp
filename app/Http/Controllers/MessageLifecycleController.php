<?php

namespace App\Http\Controllers;

use App\Models\WishMessages;
use App\Models\UserSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MessageLifecycleController extends Controller
{
    /**
     * Store or update the Vault PIN and Auto-Archive settings.
     */
    public function updateSettings(Request $request)
    {
        $vaultPinInput = $request->input('vault_pin');
        $hasStars = $vaultPinInput && str_contains($vaultPinInput, '*');

        $rules = [
            'auto_archive_days' => 'nullable|integer|min:1',
        ];
        if ($request->filled('vault_pin') && !$hasStars) {
            $rules['vault_pin'] = 'required|string|min:4|max:6';
        }
        $request->validate($rules);

        $settings = UserSettings::firstOrCreate(['user_id' => Auth::id()]);

        if ($request->filled('vault_pin') && !$hasStars) {
            $settings->vault_pin = \Illuminate\Support\Facades\Crypt::encryptString($request->input('vault_pin'));
        }

        $settings->auto_archive_days = $request->input('auto_archive_days');
        $settings->save();

        return redirect()->back()->with('success', 'Lifecycle settings updated.');
    }

    /**
     * Authenticate into the Vault.
     */
    public function authVault(Request $request)
    {
        $request->validate(['pin' => 'required|string']);
        
        $settings = UserSettings::where('user_id', Auth::id())->first();
        if (!$settings || !$settings->vault_pin) {
            return redirect()->back()->with('error', 'Vault PIN not configured.');
        }

        $isCorrect = false;
        try {
            $decrypted = \Illuminate\Support\Facades\Crypt::decryptString($settings->vault_pin);
            $isCorrect = ($request->input('pin') === $decrypted);
        } catch (\Exception $e) {
            $isCorrect = Hash::check($request->input('pin'), $settings->vault_pin);
        }

        if ($isCorrect) {
            session(['vault_unlocked' => true]);
            return redirect()->route('user.vault.page');
        }

        return redirect()->back()->with('error', 'Incorrect Vault PIN.');
    }

    private function getLayoutData($user)
    {
        return [
            'user' => $user,
            'userSettings' => UserSettings::firstOrCreate(['user_id' => $user->id]),
            'notifications' => \App\Models\UserNotification::where('user_id', $user->id)->latest()->get(),
            'allMessagesForEdit' => WishMessages::where('user_id', $user->id)->get(),
            'templateList' => ['view-1', 'view-2', 'view-3', 'view-4', 'view-5'],
            'dashboardStats' => [
                'total_views' => \App\Models\MessageViews::where('user_id', $user->id)->count(),
                'active_messages' => WishMessages::where('user_id', $user->id)->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at', '>', now()); })->count(),
            ],
            'viewsLast7Days' => [],
        ];
    }

    /**
     * View the Vault page.
     */
    public function vaultPage()
    {
        $user = Auth::user();
        $data = $this->getLayoutData($user);

        if (!session('vault_unlocked')) {
            return view('user.pages.security.vault-auth', $data);
        }

        $data['messages'] = WishMessages::forUser()->where('is_vaulted', true)->orderBy('created_at', 'desc')->get();
        return view('user.pages.security.vault', $data);
    }

    /**
     * Close Vault session.
     */
    public function lockVault()
    {
        session()->forget('vault_unlocked');
        return redirect()->route('user.settings.messages.page')->with('success', 'Vault locked.');
    }

    /**
     * Toggle Vault status for a message.
     */
    public function toggleVault($id)
    {
        $message = WishMessages::forUser()->findOrFail($id);
        $message->is_vaulted = !$message->is_vaulted;
        $message->save();
        
        return redirect()->back()->with('success', $message->is_vaulted ? 'Message vaulted.' : 'Message removed from vault.');
    }

    /**
     * Update the specific Vault PIN for a message.
     */
    public function updateSpecificPin(Request $request, $id)
    {
        $request->validate(['specific_vault_pin' => 'nullable|string|max:255']);
        
        $message = WishMessages::forUser()->findOrFail($id);
        
        if ($request->filled('specific_vault_pin')) {
            if ($request->input('specific_vault_pin') !== $message->specific_vault_pin) {
                $message->specific_vault_pin = \Illuminate\Support\Facades\Crypt::encryptString($request->input('specific_vault_pin'));
            }
        } else {
            $message->specific_vault_pin = null;
        }
        $message->save();
        
        return redirect()->back()->with('success', 'Specific Vault PIN updated.');
    }

    /**
     * Toggle Archive status for a message.
     */
    public function toggleArchive($id)
    {
        $message = WishMessages::forUser()->findOrFail($id);
        $message->is_archived = !$message->is_archived;
        $message->save();
        
        return redirect()->back()->with('success', $message->is_archived ? 'Message archived.' : 'Message unarchived.');
    }

    /**
     * Manually trigger auto-archive rule.
     */
    public function runAutoArchive()
    {
        $settings = UserSettings::where('user_id', Auth::id())->first();
        if (!$settings || !$settings->auto_archive_days) {
            return redirect()->back()->with('error', 'No auto-archive rule defined.');
        }

        $cutoff = Carbon::now()->subDays($settings->auto_archive_days);
        
        $count = WishMessages::forUser()
            ->where('is_archived', false)
            ->where('is_vaulted', false)
            ->where('created_at', '<', $cutoff)
            ->update(['is_archived' => true]);

        return redirect()->back()->with('success', "Auto-archive ran. Archived {$count} old messages.");
    }

    /**
     * View the Trash / Recently Deleted page.
     */
    public function trashPage()
    {
        $user = Auth::user();
        $data = $this->getLayoutData($user);
        $data['messages'] = WishMessages::forUser()->onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        return view('user.pages.general.trash', $data);
    }

    /**
     * Restore a soft-deleted message.
     */
    public function restore($id)
    {
        $message = WishMessages::forUser()->onlyTrashed()->findOrFail($id);
        $message->restore();
        
        return redirect()->back()->with('success', 'Message restored successfully.');
    }

    /**
     * Permanently delete a message.
     */
    public function forceDelete($id)
    {
        $message = WishMessages::forUser()->onlyTrashed()->findOrFail($id);
        $message->forceDelete();
        
        return redirect()->back()->with('success', 'Message permanently deleted.');
    }
}
