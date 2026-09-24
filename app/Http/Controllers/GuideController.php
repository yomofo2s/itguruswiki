<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class GuideController extends Controller
{
    public function index(Request $request): View
    {
        return $this->listing($request, null);
    }

    public function category(Request $request, Category $category): View
    {
        return $this->listing($request, $category);
    }

    public function show(Article $article): View
    {
        Gate::authorize('view', $article);

        if ($article->isPublished()) {
            Article::withoutTimestamps(fn () => $article->increment('views'));
        }

        $article->load('category', 'author');

        return view('guides.show', [
            'article' => $article,
            'related' => Article::published()->with('category', 'author')
                ->where('category_id', $article->category_id)
                ->whereKeyNot($article->getKey())
                ->latest('published_at')->limit(3)->get(),
        ]);
    }

    private function listing(Request $request, ?Category $category): View
    {
        $q = trim((string) $request->query('q', ''));

        $articles = Article::published()
            ->with('category', 'author')
            ->when($category, fn ($query) => $query->whereBelongsTo($category))
            ->search($q)
            ->orderByDesc('featured')
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        return view('guides.index', [
            'articles' => $articles,
            'category' => $category,
            'categories' => Category::orderBy('sort_order')->get(),
            'q' => $q,
        ]);
    }
}
