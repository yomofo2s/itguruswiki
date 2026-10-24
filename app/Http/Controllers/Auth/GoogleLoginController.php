<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleLoginController extends Controller
{
    public function redirect(): RedirectResponse
    {
        abort_unless(config('services.google.client_id'), 404);

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        abort_unless(config('services.google.client_id'), 404);

        try {
            $google = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('login')->withErrors(['email' => 'Google sign-in failed. Please try again.']);
        }

        // Only trust an email address Google itself has verified.
        if (! $google->getEmail() || ! ($google->user['email_verified'] ?? false)) {
            return redirect()->route('login')->withErrors(['email' => 'Your Google email address is not verified.']);
        }

        $email = Str::lower($google->getEmail());
        $user = User::where('google_id', $google->getId())->first() ?? User::where('email', $email)->first();

        if (! $user) {
            $user = new User(['name' => $google->getName() ?: Str::before($email, '@'), 'email' => $email, 'password' => Str::random(40)]);
        } elseif (! $user->hasVerifiedEmail()) {
            // Someone may have pre-registered this address with a password they know: invalidate it.
            $user->password = Str::random(40);
        }

        $user->google_id = $google->getId();
        $user->email_verified_at ??= now();
        $user->save();

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
