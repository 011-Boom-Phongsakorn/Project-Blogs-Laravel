<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        // If user is logged in
        if ($request->user()) {
            if ($request->user()->hasVerifiedEmail()) {
                return redirect()->intended(route('home', absolute: false));
            }

            $request->user()->sendEmailVerificationNotification();

            return back()->with('status', 'verification-link-sent');
        }

        // If user is not logged in, find by email
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();

            return back()->with('success', 'Verification email has been sent! Please check your inbox.');
        }

        if ($user && $user->hasVerifiedEmail()) {
            return back()->withErrors(['email' => 'This email is already verified. You can login now.']);
        }

        return back()->withErrors(['email' => 'User not found.']);
    }
}
