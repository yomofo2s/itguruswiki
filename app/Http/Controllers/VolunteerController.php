<?php

namespace App\Http\Controllers;

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
        ]);

        $volunteer = Volunteer::create(Arr::except($data, 'website'));

        SafeMail::send(fn () => Notification::route('mail', config('itgurus.notify_email'))
            ->notify(new VolunteerSignedUp($volunteer)));

        return redirect()->to(route('community').'#volunteer')
            ->with('status', 'Welcome aboard! We will get in touch with you soon.');
    }
}
