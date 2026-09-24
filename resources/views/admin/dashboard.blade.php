<x-layouts.admin title="Overview">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ([
            ['pending', 'Guides waiting for review', 'admin.articles.index', ['status' => 'pending'], 'clock'],
            ['published', 'Published guides', 'admin.articles.index', ['status' => 'published'], 'book'],
            ['posts', 'Published news & events', 'admin.posts.index', [], 'news'],
            ['unread', 'Unread messages', 'admin.messages.index', [], 'inbox'],
            ['volunteers', 'New volunteers', 'admin.volunteers.index', [], 'family'],
            ['users', 'Registered users', Gate::allows('manage-users') ? 'admin.users.index' : null, [], 'user'],
        ] as [$key, $label, $route, $params, $icon])
            <a href="{{ $route ? route($route, $params) : '#' }}" class="card flex items-center gap-4 p-5 transition hover:shadow-md">
                <span @class(['grid size-11 place-items-center rounded-xl', 'bg-amber-50 text-amber-700' => in_array($key, ['pending', 'unread', 'volunteers']) && $counts[$key] > 0, 'bg-brand-50 text-brand-700' => ! (in_array($key, ['pending', 'unread', 'volunteers']) && $counts[$key] > 0)])>
                    <x-icon :name="$icon" class="size-6" />
                </span>
                <div>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($counts[$key]) }}</p>
                    <p class="text-sm text-slate-600">{{ $label }}</p>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-2">
        <section class="card">
            <h2 class="border-b border-slate-100 px-5 py-4 font-semibold">Review queue</h2>
            <ul class="divide-y divide-slate-100">
                @forelse ($pending as $article)
                    <li class="flex items-center justify-between gap-3 px-5 py-3">
                        <div class="min-w-0">
                            <a href="{{ route('guides.show', $article) }}" class="block truncate font-medium hover:text-brand-700">{{ $article->title }}</a>
                            <p class="text-xs text-slate-500">{{ $article->author?->name ?? 'Deleted user' }} · {{ $article->category->name }} · {{ $article->updated_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('admin.articles.index', ['status' => 'pending']) }}" class="btn-secondary btn-sm">Review</a>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-slate-500">All caught up.</li>
                @endforelse
            </ul>
        </section>
        <section class="card">
            <h2 class="border-b border-slate-100 px-5 py-4 font-semibold">Most read guides</h2>
            <ul class="divide-y divide-slate-100">
                @forelse ($popular as $article)
                    <li class="flex items-center justify-between gap-3 px-5 py-3">
                        <a href="{{ route('guides.show', $article) }}" class="truncate font-medium hover:text-brand-700">{{ $article->title }}</a>
                        <span class="inline-flex shrink-0 items-center gap-1 text-sm text-slate-500"><x-icon name="eye" class="size-4" />{{ number_format($article->views) }}</span>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-sm text-slate-500">No published guides yet.</li>
                @endforelse
            </ul>
        </section>
    </div>
</x-layouts.admin>
