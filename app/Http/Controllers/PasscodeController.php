<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasscodeController extends Controller
{
    /**
     * Show the passcode verification page
     */
    public function showVerify()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }

        // If they don't have a passcode or already verified, skip
        $user = Auth::user();
        $redirectRoute = $user->role === 'admin' ? 'admin.page' : 'user.page';
        
        if (!$user->settings || empty($user->settings->login_passcode) || session('passcode_verified') === true) {
            return redirect()->route($redirectRoute);
        }

        return view('auth.passcode');
    }

    /**
     * Verify the entered passcode
     */
    public function verify(Request $request)
    {
        $request->validate([
            'passcode' => 'required|string',
        ]);

        $user = Auth::user();

        if (Hash::check($request->passcode, $user->settings->login_passcode)) {
            session(['passcode_verified' => true]);
            $redirectRoute = $user->role === 'admin' ? 'admin.page' : 'user.page';
            return redirect()->intended(route($redirectRoute));
        }

        return back()->withErrors(['passcode' => 'Incorrect passcode. Please try again.']);
    }
}
