@props(['article', 'showCategory' => true])
<article class="group card relative flex flex-col overflow-hidden transition hover:-translate-y-0.5 hover:shadow-md">
    @if ($article->coverUrl())
        <img src="{{ $article->coverUrl() }}" alt="" class="aspect-[16/9] w-full object-cover" loading="lazy">
    @endif
    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-center gap-2 text-xs">
            @if ($showCategory && $article->relationLoaded('category'))
                <span class="badge bg-brand-50 text-brand-800">{{ $article->category->name }}</span>
            @endif
            @if ($article->featured)
                <span class="badge bg-gold-300/40 text-amber-900"><x-icon name="star" class="mr-1 size-3" />Featured</span>
            @endif
        </div>
        <h3 class="mt-3 text-lg font-semibold leading-snug text-slate-900 group-hover:text-brand-700">
            <a href="{{ route('guides.show', $article) }}"><span class="absolute inset-0"></span>{{ $article->title }}</a>
        </h3>
        @if ($article->excerpt)
            <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">{{ $article->excerpt }}</p>
        @endif
        <div class="mt-auto flex items-center gap-3 pt-4 text-xs text-slate-500">
            <span class="inline-flex items-center gap-1"><x-icon name="clock" class="size-3.5" />{{ $article->readingTime() }} min read</span>
            @if ($article->published_at)
                <span>{{ $article->published_at->format('d M Y') }}</span>
            @endif
        </div>
    </div>
</article>
