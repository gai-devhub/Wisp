<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Subscriber;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriberWelcomeMail;

class SubscriberController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $subscriber = Subscriber::where('email', $request->email)->first();

        if ($subscriber) {
            if ($subscriber->status === 'unsubscribed') {
                $subscriber->update(['status' => 'active']);
                return back()->with('success', 'You have been re-subscribed to the WISP newsletter!');
            }
            return back()->with('info', 'You are already subscribed to the newsletter.');
        }

        $subscriber = Subscriber::create([
            'email' => $request->email,
            'token' => Str::random(32),
            'status' => 'active',
        ]);

        Mail::to($subscriber->email)->send(new SubscriberWelcomeMail($subscriber));

        return back()->with('success', 'Thank you for subscribing! Check your email for a welcome message.');
    }

    public function unsubscribe($token)
    {
        $subscriber = Subscriber::where('token', $token)->firstOrFail();
        $subscriber->update(['status' => 'unsubscribed']);

        return view('sub-folder.unsubscribe', compact('subscriber'));
    }
}
