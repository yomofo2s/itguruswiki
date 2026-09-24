@php($editing = $article->exists)
<x-layouts.app :title="$editing ? 'Edit guide' : 'New guide'">
    <div class="container-page py-10">
        <a href="{{ route('dashboard') }}" class="text-sm text-slate-500 hover:text-slate-900">&larr; My guides</a>
        <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ $editing ? 'Edit guide' : 'Write a new guide' }}</h1>

        @if ($article->status === \App\Enums\ArticleStatus::Rejected && $article->review_note)
            <div class="mt-4 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-800 ring-1 ring-rose-200"><strong>Editor feedback:</strong> {{ $article->review_note }}</div>
        @endif

        <form method="POST" enctype="multipart/form-data"
              action="{{ $editing ? route('dashboard.articles.update', $article) : route('dashboard.articles.store') }}"
              class="mt-8 grid gap-8 lg:grid-cols-[1fr_300px]">
            @csrf
            @if ($editing) @method('PUT') @endif

            <div class="card space-y-6 p-6">
                <x-form.input name="title" label="Title" :value="$article->title" required maxlength="200" placeholder="How to open a blocked account for your student visa" />
                <div>
                    <x-form.textarea name="excerpt" label="Summary" :value="$article->excerpt" rows="2" maxlength="500" data-counter="excerpt-count" hint="One or two sentences shown in search results." />
                    <p id="excerpt-count" class="mt-1 text-right text-xs text-slate-400"></p>
                </div>
                <x-markdown-editor :value="$article->body" required />
            </div>

            <div class="space-y-6">
                <div class="card space-y-5 p-5">
                    <x-form.select name="category_id" label="Topic" :options="$categories->pluck('name', 'id')" :value="$article->category_id" placeholder="Choose a topic…" required />
                    <x-form.input name="source_url" label="Official source (recommended)" type="url" :value="$article->source_url" placeholder="https://www.make-it-in-germany.com/…" />
                    <div>
                        <x-form.input name="cover" label="Cover image (optional)" type="file" accept="image/jpeg,image/png,image/webp" hint="JPG, PNG or WebP, max 2 MB." />
                        @if ($article->cover_path)
                            <div class="mt-3 flex items-center gap-3">
                                <img src="{{ $article->coverUrl() }}" alt="" class="h-14 w-24 rounded-lg object-cover">
                                <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="remove_cover" value="1" class="rounded border-slate-300"> Remove</label>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card space-y-3 p-5">
                    @if ($article->isPublished())
                        <button name="action" value="publish" class="btn-primary w-full">Save changes</button>
                    @else
                        @if (auth()->user()->canModerate())
                            <button name="action" value="publish" class="btn-primary w-full">Publish now</button>
                        @endif
                        <button name="action" value="submit" @class([auth()->user()->canModerate() ? 'btn-secondary w-full' : 'btn-primary w-full'])>Submit for review</button>
                        <button name="action" value="draft" class="btn-secondary w-full">Save as draft</button>
                    @endif
                    <p class="text-xs text-slate-500">Please follow our <a href="{{ route('conduct') }}" class="underline" target="_blank">code of conduct</a>. Don't include personal data of others.</p>
                </div>
            </div>
        </form>

        @if ($editing)
            @can('delete', $article)
                <form method="POST" action="{{ route('dashboard.articles.destroy', $article) }}" data-confirm="Delete this guide permanently?" class="mt-6">
                    @csrf @method('DELETE')
                    <button class="inline-flex items-center gap-1 text-sm font-medium text-rose-600 hover:text-rose-700"><x-icon name="trash" class="size-4" /> Delete guide</button>
                </form>
            @endcan
        @endif
    </div>
</x-layouts.app>
