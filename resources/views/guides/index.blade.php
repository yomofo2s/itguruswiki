<x-layouts.app :title="$category?->name ?? 'Guides'" :description="$category?->description">
    <x-page-header :title="$category?->name ?? 'Guides for life in Germany'" eyebrow="Knowledge base">
        {{ $category?->description ?? 'Search step-by-step guides written by the community and reviewed by our editors.' }}
    </x-page-header>

    <div class="container-page py-10 lg:grid lg:grid-cols-[240px_1fr] lg:gap-10">
        <aside class="mb-8 lg:mb-0">
            <form id="search" action="{{ $category ? route('guides.category', $category) : route('guides.index') }}" method="GET" role="search" class="relative">
                <label for="q" class="sr-only">Search</label>
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                <input id="q" type="search" name="q" value="{{ $q }}" placeholder="Search guides…" class="input pl-9">
            </form>
            <nav class="mt-6" aria-label="Topics">
                <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Topics</p>
                <ul class="mt-2 flex gap-1 overflow-x-auto lg:flex-col">
                    <li><a href="{{ route('guides.index', array_filter(['q' => $q])) }}" @class(['nav-link flex shrink-0 items-center gap-2', 'nav-link-active' => ! $category])><x-icon name="book" class="size-4" /> All topics</a></li>
                    @foreach ($categories as $cat)
                        <li>
                            <a href="{{ route('guides.category', [$cat] + array_filter(['q' => $q])) }}" @class(['nav-link flex shrink-0 items-center gap-2', 'nav-link-active' => $category?->is($cat)])>
                                <x-icon :name="$cat->icon ?? 'book'" class="size-4" /> {{ $cat->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </aside>

        <div>
            @if ($q !== '')
                <p class="mb-6 text-sm text-slate-600">
                    {{ trans_choice(':count result|:count results', $articles->total()) }} for <strong class="text-slate-900">"{{ $q }}"</strong>
                    · <a href="{{ $category ? route('guides.category', $category) : route('guides.index') }}" class="text-brand-700 underline">clear</a>
                </p>
            @endif

            @if ($articles->isEmpty())
                <div class="card grid place-items-center px-6 py-16 text-center">
                    <x-icon name="book" class="size-10 text-slate-300" />
                    <h2 class="mt-4 font-semibold text-slate-900">No guides found</h2>
                    <p class="mt-1 max-w-sm text-sm text-slate-600">Can't find what you need? Ask us, or share what you know so the next person finds it here.</p>
                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('contact') }}" class="btn-secondary">Ask a question</a>
                        <a href="{{ auth()->check() ? route('dashboard.articles.create') : route('register') }}" class="btn-primary">Write a guide</a>
                    </div>
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($articles as $article)
                        <x-article-card :article="$article" :show-category="! $category" />
                    @endforeach
                </div>
                <div class="mt-10">{{ $articles->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app>
