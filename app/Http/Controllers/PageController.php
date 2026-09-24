<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Volunteer;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about');
    }

    public function conduct(): View
    {
        return view('pages.code-of-conduct');
    }

    public function community(): View
    {
        return view('pages.community', [
            'events' => Post::upcomingEvents()->limit(4)->get(),
            'interests' => Volunteer::INTERESTS,
        ]);
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }
}
