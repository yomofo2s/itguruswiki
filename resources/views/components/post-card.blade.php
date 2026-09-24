@props(['post'])
<article class="group card relative flex flex-col overflow-hidden transition hover:-translate-y-0.5 hover:shadow-md">
    @if ($post->coverUrl())
        <img src="{{ $post->coverUrl() }}" alt="" class="aspect-[16/9] w-full object-cover" loading="lazy">
    @endif
    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-center gap-2 text-xs">
            <span @class(['badge', 'bg-gold-300/40 text-amber-900' => $post->isEvent(), 'bg-slate-100 text-slate-700' => ! $post->isEvent()])>{{ $post->type->label() }}</span>
            <span class="text-slate-500">{{ $post->published_at?->format('d M Y') }}</span>
        </div>
        <h3 class="mt-3 text-lg font-semibold leading-snug text-slate-900 group-hover:text-brand-700">
            <a href="{{ route('news.show', $post) }}"><span class="absolute inset-0"></span>{{ $post->title }}</a>
        </h3>
        @if ($post->isEvent() && $post->event_starts_at)
            <p class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-sm text-slate-600">
                <span class="inline-flex items-center gap-1"><x-icon name="calendar" class="size-4" />{{ $post->event_starts_at->format('D, d M Y · H:i') }}</span>
                @if ($post->event_location)
                    <span class="inline-flex items-center gap-1"><x-icon name="map-pin" class="size-4" />{{ $post->event_location }}</span>
                @endif
            </p>
        @endif
        @if ($post->excerpt)
            <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">{{ $post->excerpt }}</p>
        @endif
    </div>
</article>
