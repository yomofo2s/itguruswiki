@props(['title', 'subtitle' => null])
<x-layouts.app :title="$title">
    <div class="relative isolate overflow-hidden">
        <div class="absolute inset-x-0 top-0 -z-10 h-80 bg-gradient-to-b from-brand-50 to-white"></div>
        <div class="mx-auto max-w-md px-4 py-16 sm:py-20">
            <div class="text-center">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $title }}</h1>
                @if ($subtitle)
                    <p class="mt-2 text-sm text-slate-600">{{ $subtitle }}</p>
                @endif
            </div>
            <div class="card mt-8 p-6 sm:p-8">
                {{ $slot }}
            </div>
            {{ $footer ?? '' }}
        </div>
    </div>
</x-layouts.app>
