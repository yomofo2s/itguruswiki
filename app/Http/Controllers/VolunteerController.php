<?php

namespace App\Http\Controllers;

use App\Support\FormTimer;
use App\Support\SafeMail;
use App\Models\Volunteer;
use App\Notifications\VolunteerSignedUp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class VolunteerController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'interest' => ['required', Rule::in(array_keys(Volunteer::INTERESTS))],
            'skills' => ['nullable', 'string', 'max:500'],
            'message' => ['nullable', 'string', 'max:2000'],
            'website' => ['prohibited'],
            'started_at' => ['required', 'string'],
        ]);

        abort_unless(FormTimer::passes($data['started_at']), 422, 'Please reload the page and try again.');

        // Links in the name or skills are a spam signal; a repeat sign-up adds nothing.
        abort_if(preg_match('~https?://|www\.~i', $data['name'].' '.($data['skills'] ?? '')), 422);

        $volunteer = Volunteer::firstOrCreate(
            ['email' => $data['email']],
            Arr::except($data, ['website', 'started_at', 'email']),
        );

        // Store everything, but cap alert emails so a flood can't fill the inbox.
        if ($volunteer->wasRecentlyCreated && Volunteer::where('created_at', '>=', now()->subHour())->count() <= 10) {
            SafeMail::send(fn () => Notification::route('mail', config('itgurus.notify_email'))
                ->notify(new VolunteerSignedUp($volunteer)));
        }

        return redirect()->to(route('community').'#volunteer')
            ->with('status', 'Welcome aboard! We will get in touch with you soon.');
    }
}
