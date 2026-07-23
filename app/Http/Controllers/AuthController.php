<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Mail\WelcomeEmail;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.auth');
    }

    /**
     * Show account blocked page with admin contact options (email, WhatsApp, phone).
     */
    public function showAccountBlocked()
    {
        $contact_email = '';
        $contact_phone = '';
        $contact_whatsapp = '';
        if (Schema::hasTable('system_settings')) {
            $contact_email = (string) DB::table('system_settings')->where('key', 'contact_email')->value('value');
            $contact_phone = (string) DB::table('system_settings')->where('key', 'contact_phone')->value('value');
            $contact_whatsapp = (string) DB::table('system_settings')->where('key', 'contact_whatsapp')->value('value');
        }
        return view('sub-folder.account-blocked', [
            'contact_email' => $contact_email,
            'contact_phone' => $contact_phone,
            'contact_whatsapp' => $contact_whatsapp,
        ]);
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $remember = $request->has('remember');

        if ($request->expectsJson()) {
            $user = User::where('email', $request->email)->first();
            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The provided credentials do not match our records.'
                ], 401);
            }

            if ($user->status === 'blocked') {
                return response()->json([
                    'success' => false,
                    'message' => 'Account blocked.'
                ], 403);
            }

            $user->update(['last_login_at' => now()]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Logged in successfully.',
                'token' => $token,
                'user' => [
                    'id' => (string)$user->id,
                    'firstName' => explode(' ', $user->name)[0] ?? $user->name,
                    'lastName' => implode(' ', array_slice(explode(' ', $user->name), 1)) ?? '',
                    'username' => $user->username,
                    'email' => $user->email,
                    'profileImage' => $user->profile_picture_url,
                ]
            ]);
        }

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if ($user->status === 'blocked') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('account.blocked');
            }

            $user->update(['last_login_at' => now()]);
            $request->session()->regenerate();

            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\LoginAlert($user));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send login alert email: ' . $e->getMessage());
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.page');
            }

            if (Schema::hasTable('system_settings') && (bool) DB::table('system_settings')->where('key', 'app_locked')->value('value')) {
                return redirect()->route('app.locked');
            }

            if ($user->settings && !empty($user->settings->login_passcode)) {
                $request->session()->put('passcode_verified', false);
            }

            return redirect()->route('user.page');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle registration request
     */
    
    public function register(Request $request)
    {
        if (Schema::hasTable('system_settings') && (bool) DB::table('system_settings')->where('key', 'registration_disabled')->value('value')) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Registration is temporarily disabled.'], 422);
            }
            return redirect()->back()->withErrors(['registration' => 'Registration is temporarily disabled. Please try again later.'])->withInput();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:3|max:255|unique:users,username|regex:/^[a-zA-Z0-9_.-]+$/',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required'
        ], [
            'username.unique' => 'This username is already taken.',
            'username.regex' => 'Username may only contain letters, numbers, dots, hyphens and underscores.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first(), 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role = ($request->email === 'gai.dev.official@gmail.com') ? 'admin' : 'user';

        $user = User::create([
            'name' => $request->name,
            'username' => Str::lower($request->username),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);

        if (class_exists(ActivityLog::class) && \Illuminate\Support\Facades\Schema::hasTable('activity_log')) {
            ActivityLog::log($user->id, 'New user registered', 'Username: ' . $user->username);
        }

        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send welcome email: ' . $e->getMessage());
        }

        $user->update(['last_login_at' => now()]);

        if ($request->expectsJson()) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'success' => true,
                'message' => 'Account created successfully!',
                'token' => $token,
                'user' => [
                    'id' => (string)$user->id,
                    'firstName' => explode(' ', $user->name)[0] ?? $user->name,
                    'lastName' => implode(' ', array_slice(explode(' ', $user->name), 1)) ?? '',
                    'username' => $user->username,
                    'email' => $user->email,
                    'profileImage' => $user->profile_picture_url,
                ]
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('user.page')->with('success', 'Account created successfully!');
    }


    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
            return redirect()->route('auth.login')->with('error', 'Google sign-in is not configured.');
        }
        return Socialite::driver('google')
            ->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
            return redirect()->route('auth.login')->with('error', 'Google sign-in is not configured.');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('auth.login')->with('error', 'Google sign-in was cancelled or failed. Please try again.');
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            $user = User::where('email', $googleUser->getEmail())->first();
        }

        if ($user) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'profile_picture' => $googleUser->getAvatar(),
                'last_login_at' => now(),
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken ?? $user->google_refresh_token,
            ]);
        } else {
            if (Schema::hasTable('system_settings') && (bool) DB::table('system_settings')->where('key', 'registration_disabled')->value('value')) {
                return redirect()->route('auth.login')->with('error', 'Registration is temporarily disabled.');
            }

            $name = $googleUser->getName() ?: $googleUser->getEmail();
            $baseUsername = Str::lower(Str::slug(Str::before($googleUser->getEmail(), '@')) ?: 'user');
            $username = $baseUsername;
            $suffix = 0;
            while (User::where('username', $username)->exists()) {
                $suffix++;
                $username = $baseUsername . (string) $suffix;
            }

            $role = ($googleUser->getEmail() === 'gaicorporation.official@gmail.com') ? 'admin' : 'user';

            $user = User::create([
                'name' => $name,
                'username' => $username,
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(Str::random(32)),
                'role' => $role,
                'google_id' => $googleUser->getId(),
                'profile_picture' => $googleUser->getAvatar(),
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
            ]);

            if (class_exists(ActivityLog::class) && Schema::hasTable('activity_log')) {
                ActivityLog::log($user->id, 'New user registered via Google', 'Username: ' . $user->username);
            }

            try {
                Mail::to($user->email)->send(new WelcomeEmail($user));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send welcome email: ' . $e->getMessage());
            }
        }

        if ($user->status === 'blocked') {
            return redirect()->route('account.blocked');
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\LoginAlert($user));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send login alert email: ' . $e->getMessage());
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.page');
        }
        if (Schema::hasTable('system_settings') && (bool) DB::table('system_settings')->where('key', 'app_locked')->value('value')) {
            return redirect()->route('app.locked');
        }
        if ($user->settings && !empty($user->settings->login_passcode)) {
            $request->session()->put('passcode_verified', false);
        }
        return redirect()->route('user.page')->with('success', 'Signed in with Google.');
    }

    /**
     * Redirect to Spotify OAuth
     */
    public function redirectToSpotify()
    {
        if (empty(config('services.spotify.client_id')) || empty(config('services.spotify.client_secret'))) {
            return redirect()->route('auth.login')->with('error', 'Spotify sign-in is not configured.');
        }
        return Socialite::driver('spotify')
            ->scopes(['user-read-email', 'user-read-private'])
            ->redirect();
    }

    /**
     * Handle Spotify OAuth callback
     */
    public function handleSpotifyCallback(Request $request)
    {
        if (empty(config('services.spotify.client_id')) || empty(config('services.spotify.client_secret'))) {
            return redirect()->route('auth.login')->with('error', 'Spotify sign-in is not configured.');
        }

        try {
            $spotifyUser = Socialite::driver('spotify')->user();
        } catch (\Throwable $e) {
            return redirect()->route('auth.login')->with('error', 'Spotify sign-in was cancelled or failed. Please try again.');
        }

        $user = User::where('spotify_id', $spotifyUser->getId())->first();

        if (!$user && $spotifyUser->getEmail()) {
            $user = User::where('email', $spotifyUser->getEmail())->first();
        }

        if ($user) {
            $user->update([
                'spotify_id' => $spotifyUser->getId(),
                'profile_picture' => $user->profile_picture ?? $spotifyUser->getAvatar(),
                'last_login_at' => now(),
                'spotify_token' => $spotifyUser->token,
                'spotify_refresh_token' => $spotifyUser->refreshToken ?? $user->spotify_refresh_token,
            ]);
        } else {
            if (Schema::hasTable('system_settings') && (bool) DB::table('system_settings')->where('key', 'registration_disabled')->value('value')) {
                return redirect()->route('auth.login')->with('error', 'Registration is temporarily disabled.');
            }

            $email = $spotifyUser->getEmail() ?: $spotifyUser->getId() . '@spotify.local';
            $name = $spotifyUser->getName() ?: 'Spotify User';
            $baseUsername = Str::lower(Str::slug(Str::before($email, '@')) ?: 'user');
            $username = $baseUsername;
            $suffix = 0;
            while (User::where('username', $username)->exists()) {
                $suffix++;
                $username = $baseUsername . (string) $suffix;
            }

            $role = ($email === 'gaicorporation.official@gmail.com') ? 'admin' : 'user';

            $user = User::create([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'role' => $role,
                'spotify_id' => $spotifyUser->getId(),
                'profile_picture' => $spotifyUser->getAvatar(),
                'spotify_token' => $spotifyUser->token,
                'spotify_refresh_token' => $spotifyUser->refreshToken,
            ]);

            if (class_exists(ActivityLog::class) && Schema::hasTable('activity_log')) {
                ActivityLog::log($user->id, 'New user registered via Spotify', 'Username: ' . $user->username);
            }

            try {
                Mail::to($user->email)->send(new WelcomeEmail($user));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send welcome email: ' . $e->getMessage());
            }
        }

        if ($user->status === 'blocked') {
            return redirect()->route('account.blocked');
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\LoginAlert($user));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send login alert email: ' . $e->getMessage());
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.page');
        }
        if (Schema::hasTable('system_settings') && (bool) DB::table('system_settings')->where('key', 'app_locked')->value('value')) {
            return redirect()->route('app.locked');
        }
        if ($user->settings && !empty($user->settings->login_passcode)) {
            $request->session()->put('passcode_verified', false);
        }
        return redirect()->route('user.page')->with('success', 'Signed in with Spotify.');
    }

    /**
     * Step 1: Send reset code to email
     */
    public function sendResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'The email does not exist.'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $code = str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($code),
                'created_at' => now()
            ]
        );

        try {
            Mail::to($request->email)->send(new \App\Mail\PasswordResetCodeMail($code));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send reset code: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send verification code. Please try again later.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'Verification code sent to your email.']);
    }

    /**
     * Step 2: Verify reset code
     */
    public function verifyResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $tokenRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$tokenRecord || !Hash::check($request->code, $tokenRecord->token)) {
            return response()->json(['success' => false, 'errors' => ['code' => ['Invalid verification code.']]], 422);
        }

        // Check if token is expired (e.g., 15 minutes)
        if (now()->diffInMinutes($tokenRecord->created_at) > 15) {
            return response()->json(['success' => false, 'errors' => ['code' => ['Verification code expired. Please request a new one.']]], 422);
        }

        return response()->json(['success' => true, 'message' => 'Code verified successfully.']);
    }

    /**
     * Step 3: Update password
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $tokenRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$tokenRecord || !Hash::check($request->code, $tokenRecord->token)) {
            return response()->json(['success' => false, 'errors' => ['code' => ['Invalid verification code.']]], 422);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return response()->json(['success' => true, 'message' => 'Password updated successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'User not found.'], 404);
    }

    /**
     * Handle email verification link.
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        if (!$request->hasValidSignature()) {
            abort(401, 'Invalid or expired verification link.');
        }

        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->email))) {
            abort(401, 'Invalid verification link.');
        }

        if (is_null($user->email_verified_at)) {
            $user->update(['email_verified_at' => now()]);
        }

        return view('auth.verify-success', ['user' => $user]);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->update(['last_logout_at' => now()]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login');
    }
}

