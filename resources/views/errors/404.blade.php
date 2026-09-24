<x-layouts.app title="Page not found">
    <div class="container-page grid place-items-center py-24 text-center">
        <p class="text-sm font-semibold text-brand-700">404</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">We couldn't find that page</h1>
        <p class="mt-3 text-slate-600">It may have been moved or unpublished. Try searching the guides instead.</p>
        <div class="mt-8 flex gap-3"><a href="{{ route('home') }}" class="btn-secondary">Home</a><a href="{{ route('guides.index') }}" class="btn-primary">Search guides</a></div>
    </div>
</x-layouts.app>
