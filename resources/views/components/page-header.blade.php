@props(['title', 'eyebrow' => null])
<section class="relative isolate overflow-hidden border-b border-slate-200/70 bg-gradient-to-b from-brand-50/80 to-white">
    <div class="absolute -right-24 -top-24 -z-10 size-72 rounded-full bg-gold-300/20 blur-3xl"></div>
    <div class="container-page py-12 sm:py-16">
        @if ($eyebrow)
            <p class="text-sm font-semibold text-brand-700">{{ $eyebrow }}</p>
        @endif
        <h1 class="mt-2 max-w-3xl text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">{{ $title }}</h1>
        @if ($slot->isNotEmpty())
            <div class="mt-4 max-w-2xl text-lg leading-8 text-slate-600">{{ $slot }}</div>
        @endif
    </div>
</section>
