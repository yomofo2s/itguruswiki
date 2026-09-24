<?php

namespace App\Http\Controllers\Auth;

use App\Support\SafeMail;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function notice(Request $request): View|RedirectResponse
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->intended(route('dashboard', absolute: false))
            : view('auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()->intended(route('dashboard', absolute: false))
            ->with('status', 'Thanks! Your email address is verified.');
    }

    public function send(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $sent = SafeMail::send(fn () => $request->user()->sendEmailVerificationNotification());

        return back()->with('status', $sent
            ? 'A new verification link has been sent to your email address.'
            : 'We could not send the email right now. Please try again later or contact us.');
    }
}
