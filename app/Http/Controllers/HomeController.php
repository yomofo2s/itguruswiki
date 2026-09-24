<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featured = Article::published()->with('category', 'author')
            ->orderByDesc('featured')->latest('published_at')
            ->limit(3)->get();

        return view('home', [
            'featured' => $featured,
            'categories' => Category::withCount(['articles as published_count' => fn ($q) => $q->published()])
                ->orderBy('sort_order')->get(),
            'news' => Post::published()->latest('published_at')->limit(3)->get(),
            'events' => Post::upcomingEvents()->limit(3)->get(),
            'stats' => [
                'guides' => Article::published()->count(),
                'members' => User::whereNotNull('email_verified_at')->count(),
                'topics' => Category::count(),
            ],
        ]);
    }
}
