<x-layouts.app>
    {{-- Hero --}}
    <section class="relative isolate overflow-hidden bg-brand-950 text-white">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_top_right,_var(--color-brand-700),_transparent_60%)]"></div>
        <div class="absolute -bottom-32 -left-32 -z-10 size-96 rounded-full bg-gold-400/20 blur-3xl"></div>
        <div class="container-page grid gap-12 py-20 sm:py-28 lg:grid-cols-[1.2fr_1fr] lg:items-center">
            <div>
                <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-medium text-brand-100 ring-1 ring-white/15">
                    <x-icon name="sparkles" class="size-4 text-gold-300" /> Community knowledge, verified
                </p>
                <h1 class="mt-6 text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                    Your guide to <span class="text-gold-300">life in Germany</span>.
                </h1>
                <p class="mt-6 max-w-xl text-lg leading-8 text-brand-100/90">
                    Studying, working, bringing your family, finding a flat - the answers usually get lost in busy group chats.
                    We collect them, check them and keep them up to date, for everyone.
                </p>
                <form action="{{ route('guides.index') }}" method="GET" role="search" class="mt-8 flex max-w-xl gap-2 rounded-2xl bg-white p-2 shadow-xl">
                    <label for="hero-search" class="sr-only">Search guides</label>
                    <div class="flex flex-1 items-center gap-2 px-2 text-slate-400">
                        <x-icon name="search" />
                        <input id="hero-search" type="search" name="q" placeholder="e.g. blocked account, Blue Card, Anmeldung"
                               class="w-full border-0 bg-transparent p-1 text-slate-900 placeholder:text-slate-400 focus:ring-0">
                    </div>
                    <button class="btn-primary">Search</button>
                </form>
            </div>
            <dl class="grid grid-cols-3 gap-3 lg:grid-cols-1 lg:gap-4">
                @foreach ([['guides', 'Guides published'], ['topics', 'Topics'], ['members', 'Community members']] as [$key, $label])
                    <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur">
                        <dt class="text-xs text-brand-100/80 sm:text-sm">{{ $label }}</dt>
                        <dd class="mt-1 text-2xl font-bold sm:text-3xl">{{ number_format($stats[$key]) }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- Topics --}}
    <section class="container-page py-16 sm:py-20">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Browse by topic</h2>
                <p class="mt-2 text-slate-600">Practical, step-by-step guides written by people who have been through it.</p>
            </div>
            <a href="{{ route('guides.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-700 hover:text-brand-800">All guides <x-icon name="arrow-right" class="size-4" /></a>
        </div>
        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($categories as $category)
                <a href="{{ route('guides.category', $category) }}" class="group card p-5 transition hover:-translate-y-0.5 hover:shadow-md hover:ring-brand-300">
                    <span class="grid size-11 place-items-center rounded-xl bg-brand-50 text-brand-700 ring-1 ring-brand-100 group-hover:bg-brand-600 group-hover:text-white">
                        <x-icon :name="$category->icon ?? 'book'" class="size-6" />
                    </span>
                    <h3 class="mt-4 font-semibold text-slate-900">{{ $category->name }}</h3>
                    <p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $category->description }}</p>
                    <p class="mt-3 text-xs font-medium text-slate-500">{{ trans_choice(':count guide|:count guides', $category->published_count) }}</p>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Featured guides --}}
    @if ($featured->isNotEmpty())
        <section class="bg-slate-50 py-16 sm:py-20">
            <div class="container-page">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Popular guides</h2>
                <div class="mt-8 grid gap-6 md:grid-cols-3">
                    @foreach ($featured as $article)
                        <x-article-card :article="$article" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- News + events --}}
    <section class="container-page grid gap-12 py-16 sm:py-20 lg:grid-cols-[2fr_1fr]">
        <div>
            <div class="flex items-end justify-between gap-4">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Latest news</h2>
                <a href="{{ route('news.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-700">All news <x-icon name="arrow-right" class="size-4" /></a>
            </div>
            <div class="mt-8 grid gap-6 sm:grid-cols-2">
                @forelse ($news->take(2) as $post)
                    <x-post-card :post="$post" />
                @empty
                    <p class="text-slate-500">No news yet - check back soon.</p>
                @endforelse
            </div>
        </div>
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Upcoming events</h2>
            <div class="card mt-8 divide-y divide-slate-100 p-2">
                @forelse ($events as $event)
                    <x-event-item :event="$event" />
                @empty
                    <p class="p-4 text-sm text-slate-500">No events planned right now. Want to organise a meetup? <a href="{{ route('community') }}#volunteer" class="font-medium text-brand-700 underline">Tell us</a>.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="container-page">
        <div class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-brand-700 to-brand-900 px-6 py-14 text-white sm:px-12">
            <div class="absolute -right-20 -top-20 -z-10 size-72 rounded-full bg-gold-400/30 blur-3xl"></div>
            <div class="grid gap-8 lg:grid-cols-[2fr_1fr] lg:items-center">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Know something that would help others?</h2>
                    <p class="mt-3 max-w-2xl text-brand-100">Share your experience as a guide. Our editors check every submission before it goes live, so the information stays reliable.</p>
                </div>
                <div class="flex flex-wrap gap-3 lg:justify-end">
                    <a href="{{ auth()->check() ? route('dashboard.articles.create') : route('register') }}" class="btn-gold">
                        <x-icon name="pencil" class="size-4" /> Write a guide
                    </a>
                    <a href="{{ route('community') }}" class="btn bg-white/10 text-white ring-1 ring-white/25 hover:bg-white/20">Volunteer</a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
