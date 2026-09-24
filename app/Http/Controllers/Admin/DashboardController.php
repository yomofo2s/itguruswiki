<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ArticleStatus;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'counts' => [
                'pending' => Article::where('status', ArticleStatus::Pending)->count(),
                'published' => Article::published()->count(),
                'posts' => Post::published()->count(),
                'users' => User::count(),
                'unread' => ContactMessage::whereNull('read_at')->count(),
                'volunteers' => Volunteer::whereNull('contacted_at')->count(),
            ],
            'pending' => Article::where('status', ArticleStatus::Pending)->with('author', 'category')->oldest('updated_at')->limit(5)->get(),
            'popular' => Article::published()->orderByDesc('views')->limit(5)->get(),
        ]);
    }
}
