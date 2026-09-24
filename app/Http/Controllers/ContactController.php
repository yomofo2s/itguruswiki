<?php

namespace App\Http\Controllers;

use App\Support\SafeMail;
use App\Models\ContactMessage;
use App\Notifications\ContactMessageReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('pages.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'website' => ['prohibited'], // honeypot
        ]);

        $message = ContactMessage::create(Arr::except($data, 'website') + ['ip_address' => $request->ip()]);

        SafeMail::send(fn () => Notification::route('mail', config('itgurus.notify_email'))
            ->notify(new ContactMessageReceived($message)));

        return redirect()->route('contact')->with('status', 'Thank you! Your message has been sent - we usually reply within a few days.');
    }
}
