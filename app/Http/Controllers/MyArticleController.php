<?php

namespace App\Http\Controllers;

use App\Support\SafeMail;
use App\Enums\ArticleStatus;
use App\Http\Controllers\Concerns\HandlesCoverImage;
use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Notifications\ArticleSubmitted;
use App\Support\Markdown;
use App\Support\Slug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

/** Members write guides here; editors review them in the admin area. */
class MyArticleController extends Controller
{
    use HandlesCoverImage;

    public function index(Request $request): View
    {
        return view('dashboard.index', [
            'articles' => $request->user()->articles()->with('category')->latest('updated_at')->paginate(15),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Article::class);

        return view('dashboard.article-form', [
            'article' => new Article,
            'categories' => Category::orderBy('sort_order')->get(),
        ]);
    }

    public function store(ArticleRequest $request): RedirectResponse
    {
        Gate::authorize('create', Article::class);

        $article = new Article($request->safe()->only(['category_id', 'title', 'excerpt', 'body', 'source_url']));
        $article->author()->associate($request->user());
        $article->slug = Slug::unique($article, $article->title);

        return $this->persist($request, $article, 'Guide saved.');
    }

    public function edit(Article $article): View
    {
        Gate::authorize('update', $article);

        return view('dashboard.article-form', [
            'article' => $article,
            'categories' => Category::orderBy('sort_order')->get(),
        ]);
    }

    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        Gate::authorize('update', $article);

        $article->fill($request->safe()->only(['category_id', 'title', 'excerpt', 'body', 'source_url']));

        if ($article->isDirty('title') && ! $article->isPublished()) {
            $article->slug = Slug::unique($article, $article->title);
        }

        return $this->persist($request, $article, 'Guide updated.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        Gate::authorize('delete', $article);

        $this->deleteCover($article);
        $article->delete();

        return redirect()->route('dashboard')->with('status', 'Guide deleted.');
    }

    /** Markdown preview for the editor (same renderer as the public page). */
    public function preview(Request $request): JsonResponse
    {
        $data = $request->validate(['body' => ['nullable', 'string', 'max:100000']]);

        return response()->json(['html' => Markdown::toHtml($data['body'] ?? '')]);
    }

    private function persist(ArticleRequest $request, Article $article, string $message): RedirectResponse
    {
        $user = $request->user();
        $action = $request->input('action', 'draft');
        $wasPending = $article->status === ArticleStatus::Pending;

        if ($action === 'publish' && $user->canModerate()) {
            $article->status = ArticleStatus::Published;
            $article->published_at ??= now();
            $article->reviewer()->associate($user);
            $article->reviewed_at = now();
        } elseif ($action === 'submit' || ($article->isPublished() && $user->canModerate())) {
            $article->status = $article->isPublished() ? ArticleStatus::Published : ArticleStatus::Pending;
        } else {
            $article->status = ArticleStatus::Draft;
        }

        $this->syncCover($request, $article, 'covers/articles');
        $article->save();

        if ($article->status === ArticleStatus::Pending && ! $wasPending) {
            SafeMail::send(fn () => Notification::send(
                User::whereIn('role', ['admin', 'editor'])->whereKeyNot($user->getKey())->get(),
                new ArticleSubmitted($article),
            ));
            $message = 'Thanks! Your guide was sent to our editors for review.';
        }

        return redirect()->route('dashboard')->with('status', $message);
    }
}
