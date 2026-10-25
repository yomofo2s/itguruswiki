<?php

namespace App\Http\Controllers\Auth;

use App\Support\FormTimer;
use App\Support\SafeMail;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'website' => ['prohibited'], // honeypot
            'terms' => ['accepted'],
            'started_at' => ['required', 'string'],
        ]);

        if (! FormTimer::passes($data['started_at']) || preg_match('~https?://|www\.~i', $data['name'])) {
            throw ValidationException::withMessages(['email' => 'We could not create the account. Please reload the page and try again.']);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $sent = SafeMail::send(fn () => event(new Registered($user)));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('verification.notice')->with('status', $sent ? null
            : 'Your account was created, but we could not send the confirmation email right now. Please use "Resend" in a few minutes.');
    }
}
