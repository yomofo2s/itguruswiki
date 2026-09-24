<x-layouts.app :title="$post->title" :description="$post->excerpt">
    <article>
        <header class="border-b border-slate-200/70 bg-gradient-to-b from-brand-50/70 to-white">
            <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 sm:py-16">
                <a href="{{ route('news.index') }}" class="text-sm text-slate-500 hover:text-slate-900">&larr; News & events</a>
                @unless ($post->isPublished())
                    <p class="mt-4 inline-flex rounded-lg bg-amber-50 px-3 py-1.5 text-sm font-medium text-amber-800 ring-1 ring-amber-200">Draft preview</p>
                @endunless
                <p class="mt-4"><span @class(['badge', 'bg-gold-300/40 text-amber-900' => $post->isEvent(), 'bg-slate-100 text-slate-700' => ! $post->isEvent()])>{{ $post->type->label() }}</span></p>
                <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">{{ $post->title }}</h1>
                @if ($post->excerpt)
                    <p class="mt-4 text-lg leading-8 text-slate-600">{{ $post->excerpt }}</p>
                @endif
                <p class="mt-6 text-sm text-slate-500">
                    {{ $post->published_at?->format('d M Y') }}@if ($post->author) · {{ $post->author->name }}@endif
                </p>

                @if ($post->isEvent() && $post->event_starts_at)
                    <div class="card mt-8 grid gap-4 p-5 sm:grid-cols-3">
                        <div class="flex items-start gap-3"><x-icon name="calendar" class="size-5 text-brand-600" /><div><p class="text-xs text-slate-500">When</p><p class="font-medium">{{ $post->event_starts_at->format('D, d M Y · H:i') }}</p></div></div>
                        @if ($post->event_location)
                            <div class="flex items-start gap-3"><x-icon name="map-pin" class="size-5 text-brand-600" /><div><p class="text-xs text-slate-500">Where</p><p class="font-medium">{{ $post->event_location }}</p></div></div>
                        @endif
                        @if ($post->event_url)
                            <div class="flex items-center sm:justify-end"><a href="{{ $post->event_url }}" rel="noopener" target="_blank" class="btn-primary">Register <x-icon name="external" class="size-4" /></a></div>
                        @endif
                    </div>
                @endif
            </div>
        </header>
        @if ($post->coverUrl())
            <div class="mx-auto max-w-4xl px-4 pt-10 sm:px-6">
                <img src="{{ $post->coverUrl() }}" alt="" class="aspect-[2/1] w-full rounded-2xl object-cover">
            </div>
        @endif
        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6">
            <div class="prose-content">{!! $post->bodyHtml() !!}</div>
        </div>
    </article>

    @if ($more->isNotEmpty())
        <section class="container-page border-t border-slate-200 pt-12">
            <h2 class="text-xl font-bold text-slate-900">More news</h2>
            <div class="mt-6 grid gap-6 md:grid-cols-3">
                @foreach ($more as $item)
                    <x-post-card :post="$item" />
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.app>
