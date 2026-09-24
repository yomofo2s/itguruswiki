<x-layouts.app :title="$article->title" :description="$article->excerpt">
    <article>
        <header class="border-b border-slate-200/70 bg-gradient-to-b from-brand-50/70 to-white">
            <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 sm:py-16">
                <nav class="flex items-center gap-2 text-sm text-slate-500" aria-label="Breadcrumb">
                    <a href="{{ route('guides.index') }}" class="hover:text-slate-900">Guides</a>
                    <span>/</span>
                    <a href="{{ route('guides.category', $article->category) }}" class="font-medium text-brand-700 hover:text-brand-800">{{ $article->category->name }}</a>
                </nav>

                @unless ($article->isPublished())
                    <p class="mt-4 inline-flex items-center gap-2 rounded-lg bg-amber-50 px-3 py-1.5 text-sm font-medium text-amber-800 ring-1 ring-amber-200">
                        <x-icon name="eye" class="size-4" /> Preview - {{ $article->status->label() }}
                    </p>
                @endunless

                <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">{{ $article->title }}</h1>
                @if ($article->excerpt)
                    <p class="mt-4 text-lg leading-8 text-slate-600">{{ $article->excerpt }}</p>
                @endif

                <div class="mt-6 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-slate-500">
                    @if ($article->author)
                        <span class="inline-flex items-center gap-2">
                            <span class="grid size-7 place-items-center rounded-full bg-brand-600 text-[11px] font-bold text-white">{{ $article->author->initials() }}</span>
                            {{ $article->author->name }}
                        </span>
                    @endif
                    @if ($article->published_at)
                        <span>Published {{ $article->published_at->format('d M Y') }}</span>
                    @endif
                    <span>Updated {{ $article->updated_at->format('d M Y') }}</span>
                    <span class="inline-flex items-center gap-1"><x-icon name="clock" class="size-4" />{{ $article->readingTime() }} min read</span>
                </div>

                @can('update', $article)
                    <a href="{{ route('dashboard.articles.edit', $article) }}" class="btn-secondary btn-sm mt-6"><x-icon name="pencil" class="size-4" /> Edit</a>
                @endcan
            </div>
        </header>

        @if ($article->coverUrl())
            <div class="mx-auto max-w-4xl px-4 pt-10 sm:px-6">
                <img src="{{ $article->coverUrl() }}" alt="" class="aspect-[2/1] w-full rounded-2xl object-cover">
            </div>
        @endif

        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
            <div class="prose-content">{!! $article->bodyHtml() !!}</div>

            <aside class="mt-12 rounded-2xl bg-slate-50 p-5 text-sm text-slate-600 ring-1 ring-slate-200">
                <p class="flex items-start gap-2">
                    <x-icon name="shield" class="mt-0.5 size-5 shrink-0 text-brand-600" />
                    <span>This guide was written by a community member and checked by our editors. Rules change - always confirm with the official authority.
                        @if ($article->source_url)
                            Source: <a href="{{ $article->source_url }}" rel="nofollow noopener" target="_blank" class="font-medium text-brand-700 underline break-all">{{ parse_url($article->source_url, PHP_URL_HOST) }}</a>.
                        @endif
                        Spotted something outdated? <a href="{{ route('contact', ['subject' => 'Correction: '.$article->title]) }}" class="font-medium text-brand-700 underline">Let us know</a>.
                    </span>
                </p>
            </aside>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="container-page border-t border-slate-200 pt-12">
            <h2 class="text-xl font-bold text-slate-900">More in {{ $article->category->name }}</h2>
            <div class="mt-6 grid gap-6 md:grid-cols-3">
                @foreach ($related as $item)
                    <x-article-card :article="$item" :show-category="false" />
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.app>
