<x-layouts.app :title="$type ? $type->label() : 'News & Events'">
    <x-page-header title="News & events" eyebrow="What's happening">
        Updates from the community, important changes in German rules, meetups and online sessions.
    </x-page-header>

    <div class="container-page py-10">
        <div class="flex flex-wrap gap-2" role="tablist">
            @foreach ([null => 'All', 'news' => 'News', 'event' => 'Events'] as $value => $label)
                <a href="{{ route('news.index', array_filter(['type' => $value])) }}"
                   @class(['rounded-full px-4 py-1.5 text-sm font-medium ring-1 transition',
                       'bg-brand-700 text-white ring-brand-700' => ($type?->value ?? '') === (string) $value,
                       'bg-white text-slate-700 ring-slate-200 hover:bg-slate-50' => ($type?->value ?? '') !== (string) $value])>{{ $label }}</a>
            @endforeach
        </div>

        <div class="mt-8 grid gap-10 lg:grid-cols-[1fr_300px]">
            <div>
                @if ($posts->isEmpty())
                    <div class="card px-6 py-16 text-center text-slate-500">Nothing here yet.</div>
                @else
                    <div class="grid gap-6 sm:grid-cols-2">
                        @foreach ($posts as $post)
                            <x-post-card :post="$post" />
                        @endforeach
                    </div>
                    <div class="mt-10">{{ $posts->links() }}</div>
                @endif
            </div>
            <aside>
                <h2 class="font-semibold text-slate-900">Upcoming events</h2>
                <div class="card mt-4 divide-y divide-slate-100 p-2">
                    @forelse ($events as $event)
                        <x-event-item :event="$event" />
                    @empty
                        <p class="p-4 text-sm text-slate-500">No upcoming events.</p>
                    @endforelse
                </div>
            </aside>
        </div>
    </div>
</x-layouts.app>
