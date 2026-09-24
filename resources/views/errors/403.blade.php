<x-layouts.app title="Access denied">
    <div class="container-page grid place-items-center py-24 text-center">
        <p class="text-sm font-semibold text-brand-700">403</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">You don't have access to this page</h1>
        <div class="mt-8"><a href="{{ route('home') }}" class="btn-primary">Back to home</a></div>
    </div>
</x-layouts.app>
