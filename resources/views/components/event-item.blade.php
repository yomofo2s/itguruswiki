@props(['event'])
<a href="{{ route('news.show', $event) }}" class="flex gap-4 rounded-xl p-3 transition hover:bg-slate-50">
    <div class="grid w-14 shrink-0 place-items-center rounded-xl bg-brand-700 py-2 text-center text-white">
        <span class="text-xs font-medium uppercase">{{ $event->event_starts_at->format('M') }}</span>
        <span class="text-xl font-bold leading-none">{{ $event->event_starts_at->format('d') }}</span>
    </div>
    <div class="min-w-0">
        <p class="font-semibold text-slate-900">{{ $event->title }}</p>
        <p class="mt-0.5 text-sm text-slate-500">{{ $event->event_starts_at->format('H:i') }}@if ($event->event_location) · {{ $event->event_location }}@endif</p>
    </div>
</a>
