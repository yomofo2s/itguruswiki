<?php

namespace App\Http\Controllers;

use App\Enums\PostType;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(Request $request): View
    {
        $type = PostType::tryFrom((string) $request->query('type'));

        return view('news.index', [
            'posts' => Post::published()
                ->when($type, fn ($q) => $q->where('type', $type))
                ->latest('published_at')
                ->paginate(9)
                ->withQueryString(),
            'type' => $type,
            'events' => Post::upcomingEvents()->limit(3)->get(),
        ]);
    }

    public function show(Post $post): View
    {
        abort_unless($post->isPublished() || request()->user()?->canModerate(), 404);

        return view('news.show', [
            'post' => $post->load('author'),
            'more' => Post::published()->whereKeyNot($post->getKey())->latest('published_at')->limit(3)->get(),
        ]);
    }
}
