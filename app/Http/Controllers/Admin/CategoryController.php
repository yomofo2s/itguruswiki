<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public const ICONS = ['graduation', 'passport', 'briefcase', 'code', 'family', 'home', 'health', 'sparkles', 'book'];

    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::withCount('articles')->orderBy('sort_order')->get(),
            'icons' => self::ICONS,
            'editing' => null,
        ]);
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.index', [
            'categories' => Category::withCount('articles')->orderBy('sort_order')->get(),
            'icons' => self::ICONS,
            'editing' => $category,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Category::create($this->validated($request));

        return redirect()->route('admin.categories.index')->with('status', 'Topic created.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $category->update($this->validated($request, $category));

        return redirect()->route('admin.categories.index')->with('status', 'Topic updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->articles()->exists()) {
            return back()->withErrors(['category' => 'Move or delete the guides in this topic first.']);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('status', 'Topic deleted.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('name'))]);

        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:120', Rule::unique('categories')->ignore($category)],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', Rule::in(self::ICONS)],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ]) + ['sort_order' => 0];
    }
}
