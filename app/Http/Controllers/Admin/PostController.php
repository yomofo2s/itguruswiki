<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PostType;
use App\Http\Controllers\Concerns\HandlesCoverImage;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Support\Slug;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PostController extends Controller
{
    use HandlesCoverImage;

    public function index(): View
    {
        return view('admin.posts.index', [
            'posts' => Post::with('author')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.posts.form', ['post' => new Post(['type' => PostType::News]), 'types' => PostType::cases()]);
    }

    public function store(PostRequest $request): RedirectResponse
    {
        $post = new Post;
        $post->author()->associate($request->user());

        return $this->save($request, $post, 'Post created.');
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.form', ['post' => $post, 'types' => PostType::cases()]);
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        return $this->save($request, $post, 'Post updated.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->deleteCover($post);
        $post->delete();

        return redirect()->route('admin.posts.index')->with('status', 'Post deleted.');
    }

    private function save(PostRequest $request, Post $post, string $message): RedirectResponse
    {
        $post->fill($request->safe()->except(['cover', 'remove_cover']));

        if ($post->type !== PostType::Event) {
            $post->event_starts_at = $post->event_location = $post->event_url = null;
        }

        if (! $post->exists || $post->isDirty('title')) {
            $post->slug = Slug::unique($post, $post->title);
        }

        $this->syncCover($request, $post, 'covers/posts');
        $post->save();

        return redirect()->route('admin.posts.index')->with('status', $message);
    }
}
