<?php

namespace App\Http\Controllers\Admin;

use App\Support\SafeMail;
use App\Enums\ArticleStatus;
use App\Http\Controllers\Concerns\HandlesCoverImage;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Notifications\ArticleReviewed;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Review queue and moderation. Editing itself uses the shared editor (MyArticleController). */
class ArticleController extends Controller
{
    use HandlesCoverImage;

    public function index(Request $request): View
    {
        $status = ArticleStatus::tryFrom((string) $request->query('status', 'pending'));

        return view('admin.articles.index', [
            'articles' => Article::with('author', 'category')
                ->when($status, fn ($q) => $q->where('status', $status))
                ->search($request->query('q'))
                ->latest('updated_at')
                ->paginate(20)
                ->withQueryString(),
            'status' => $status,
            'q' => $request->query('q'),
        ]);
    }

    public function publish(Request $request, Article $article): RedirectResponse
    {
        $article->status = ArticleStatus::Published;
        $article->published_at ??= now();
        $article->review_note = null;
        $this->markReviewed($request, $article);

        SafeMail::send(fn () => $article->author?->notify(new ArticleReviewed($article)));

        return back()->with('status', "\"{$article->title}\" is now live.");
    }

    public function reject(Request $request, Article $article): RedirectResponse
    {
        $data = $request->validate(['review_note' => ['required', 'string', 'max:1000']]);

        $article->status = ArticleStatus::Rejected;
        $article->review_note = $data['review_note'];
        $this->markReviewed($request, $article);

        SafeMail::send(fn () => $article->author?->notify(new ArticleReviewed($article)));

        return back()->with('status', 'Changes requested - the author has been notified.');
    }

    public function unpublish(Request $request, Article $article): RedirectResponse
    {
        $article->status = ArticleStatus::Draft;
        $this->markReviewed($request, $article);

        return back()->with('status', 'Guide unpublished.');
    }

    public function feature(Article $article): RedirectResponse
    {
        $article->featured = ! $article->featured;
        $article->save();

        return back()->with('status', $article->featured ? 'Guide featured on the home page.' : 'Guide no longer featured.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $this->deleteCover($article);
        $article->delete();

        return redirect()->route('admin.articles.index')->with('status', 'Guide deleted.');
    }

    private function markReviewed(Request $request, Article $article): void
    {
        $article->reviewer()->associate($request->user());
        $article->reviewed_at = now();
        $article->save();
    }
}
