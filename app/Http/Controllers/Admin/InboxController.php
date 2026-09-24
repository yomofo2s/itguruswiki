<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Volunteer;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InboxController extends Controller
{
    public function messages(): View
    {
        return view('admin.inbox.messages', [
            'messages' => ContactMessage::latest()->paginate(20),
        ]);
    }

    public function showMessage(ContactMessage $message): View
    {
        if ($message->read_at === null) {
            $message->forceFill(['read_at' => now()])->save();
        }

        return view('admin.inbox.message', ['message' => $message]);
    }

    public function destroyMessage(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('status', 'Message deleted.');
    }

    public function volunteers(): View
    {
        return view('admin.inbox.volunteers', [
            'volunteers' => Volunteer::latest()->paginate(25),
        ]);
    }

    public function contactVolunteer(Volunteer $volunteer): RedirectResponse
    {
        $volunteer->forceFill(['contacted_at' => $volunteer->contacted_at ? null : now()])->save();

        return back()->with('status', 'Volunteer updated.');
    }

    public function destroyVolunteer(Volunteer $volunteer): RedirectResponse
    {
        $volunteer->delete();

        return back()->with('status', 'Volunteer removed.');
    }
}
