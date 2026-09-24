@props(['title'])
@php($links = [
    ['admin.dashboard', 'admin.dashboard', 'Overview', 'chart', null],
    ['admin.articles.*', 'admin.articles.index', 'Guides', 'book', 'access-admin'],
    ['admin.posts.*', 'admin.posts.index', 'News & events', 'news', 'access-admin'],
    ['admin.categories.*', 'admin.categories.index', 'Topics', 'tag', 'access-admin'],
    ['admin.messages.*', 'admin.messages.index', 'Messages', 'inbox', 'access-admin'],
    ['admin.volunteers.*', 'admin.volunteers.index', 'Volunteers', 'family', 'access-admin'],
    ['admin.users.*', 'admin.users.index', 'Users & roles', 'user', 'manage-users'],
])
<x-layouts.app :title="$title.' · Admin'">
    <div class="container-page py-8 lg:grid lg:grid-cols-[220px_1fr] lg:gap-10">
        <aside class="mb-6 lg:mb-0">
            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">Admin</p>
            <nav class="mt-3 flex gap-1 overflow-x-auto lg:flex-col" aria-label="Admin">
                @foreach ($links as [$pattern, $route, $label, $icon, $ability])
                    @if (! $ability || Gate::allows($ability))
                        <a href="{{ route($route) }}" @class(['nav-link flex shrink-0 items-center gap-2', 'nav-link-active' => request()->routeIs($pattern)])>
                            <x-icon :name="$icon" class="size-4" /> {{ $label }}
                        </a>
                    @endif
                @endforeach
            </nav>
        </aside>
        <div class="min-w-0">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $title }}</h1>
                {{ $actions ?? '' }}
            </div>
            @if ($errors->any())
                <div class="mb-4 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-800 ring-1 ring-rose-200">{{ $errors->first() }}</div>
            @endif
            {{ $slot }}
        </div>
    </div>
</x-layouts.app>
