<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        // Get user by ID and hash from URL
        $user = User::findOrFail($request->route('id'));

        // Verify the hash matches
        if (!hash_equals(
            sha1($user->getEmailForVerification()),
            (string) $request->route('hash')
        )) {
            abort(403, 'Invalid verification link.');
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            // Auto-login and redirect to dashboard
            Auth::login($user);
            return redirect()->intended(route('dashboard', absolute: false))
                ->with('success', 'Your email is already verified. Welcome back!');
        }

        // Mark as verified
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Auto-login after successful verification
        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1')
            ->with('success', 'Email verified successfully! Welcome to Blog!');
    }
}
