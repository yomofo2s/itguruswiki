@props(['title' => null, 'description' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    <meta name="description" content="{{ $description ?? config('itgurus.tagline') }}">
    <meta property="og:title" content="{{ $title ?? config('app.name') }}">
    <meta property="og:description" content="{{ $description ?? config('itgurus.tagline') }}">
    <meta property="og:type" content="website">
    <meta name="theme-color" content="#047843">
    <link rel="icon" href="{{ asset('images/logo.jpeg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ $head ?? '' }}
</head>
<body class="flex min-h-full flex-col">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:shadow">Skip to content</a>

    <header class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/85 backdrop-blur">
        <div class="container-page flex h-16 items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo.jpeg') }}" alt="" class="size-9 rounded-xl object-cover ring-1 ring-slate-200">
                <span class="text-base font-bold tracking-tight text-slate-900">IT GURUs <span class="text-brand-700">Germany</span></span>
            </a>

            @php($nav = [
                ['guides.*', 'guides.index', 'Guides'],
                ['news.*', 'news.index', 'News & Events'],
                ['community', 'community', 'Community'],
                ['about', 'about', 'About'],
            ])

            <nav class="hidden items-center gap-1 md:flex" aria-label="Main">
                @foreach ($nav as [$pattern, $route, $label])
                    <a href="{{ route($route) }}" @class(['nav-link', 'nav-link-active' => request()->routeIs($pattern)])
                       @if (request()->routeIs($pattern)) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ route('guides.index') }}#search" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-900" title="Search guides">
                    <x-icon name="search" /><span class="sr-only">Search guides</span>
                </a>
                @auth
                    <details class="relative hidden md:block">
                        <summary class="flex cursor-pointer list-none items-center gap-2 rounded-full p-0.5 pr-3 ring-1 ring-slate-200 hover:bg-slate-50">
                            <span class="grid size-8 place-items-center rounded-full bg-brand-600 text-xs font-bold text-white">{{ auth()->user()->initials() }}</span>
                            <span class="max-w-32 truncate text-sm font-medium">{{ auth()->user()->name }}</span>
                        </summary>
                        <div class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl bg-white py-1 shadow-lg ring-1 ring-slate-200">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-slate-50"><x-icon name="pencil" class="size-4" /> My guides</a>
                            @can('access-admin')
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-slate-50"><x-icon name="chart" class="size-4" /> Admin</a>
                            @endcan
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-slate-50"><x-icon name="user" class="size-4" /> Profile</a>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                                @csrf
                                <button class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm hover:bg-slate-50"><x-icon name="logout" class="size-4" /> Log out</button>
                            </form>
                        </div>
                    </details>
                @else
                    <a href="{{ route('login') }}" class="nav-link hidden sm:inline-flex">Log in</a>
                    <a href="{{ route('register') }}" class="btn-primary hidden sm:inline-flex">Join</a>
                @endauth
                <button type="button" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden" data-toggle="mobile-menu" aria-expanded="false" aria-controls="mobile-menu">
                    <x-icon name="menu" class="size-6" /><span class="sr-only">Menu</span>
                </button>
            </div>
        </div>

        <div id="mobile-menu" class="border-t border-slate-200 bg-white md:hidden" hidden>
            <nav class="container-page flex flex-col gap-1 py-3" aria-label="Mobile">
                @foreach ($nav as [$pattern, $route, $label])
                    <a href="{{ route($route) }}" @class(['nav-link', 'nav-link-active' => request()->routeIs($pattern)])>{{ $label }}</a>
                @endforeach
                <a href="{{ route('contact') }}" class="nav-link">Contact</a>
                <div class="mt-2 border-t border-slate-100 pt-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="nav-link block">My guides</a>
                        @can('access-admin')<a href="{{ route('admin.dashboard') }}" class="nav-link block">Admin</a>@endcan
                        <a href="{{ route('profile.edit') }}" class="nav-link block">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button class="nav-link w-full text-left">Log out</button></form>
                    @else
                        <div class="flex gap-2">
                            <a href="{{ route('login') }}" class="btn-secondary flex-1">Log in</a>
                            <a href="{{ route('register') }}" class="btn-primary flex-1">Join the community</a>
                        </div>
                    @endauth
                </div>
            </nav>
        </div>
    </header>

    <main id="main" class="flex-1">
        @if (session('status'))
            <div class="container-page pt-6"><x-flash /></div>
        @endif
        {{ $slot }}
    </main>

    <footer class="mt-24 bg-slate-950 text-slate-300">
        <div class="container-page grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-4">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="" class="size-9 rounded-xl">
                    <span class="text-base font-bold text-white">IT GURUs Germany</span>
                </div>
                <p class="mt-4 max-w-sm text-sm leading-6 text-slate-400">{{ config('itgurus.tagline') }} Built by volunteers - help us make life better with good news.</p>
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach (array_filter(config('itgurus.social')) as $network => $url)
                        <a href="{{ $url }}" rel="noopener" target="_blank" class="rounded-lg bg-white/5 px-3 py-1.5 text-xs font-medium capitalize text-slate-200 ring-1 ring-white/10 hover:bg-white/10">{{ $network }}</a>
                    @endforeach
                </div>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-white">Explore</h2>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('guides.index') }}" class="hover:text-white">Guides</a></li>
                    <li><a href="{{ route('news.index') }}" class="hover:text-white">News</a></li>
                    <li><a href="{{ route('news.index', ['type' => 'event']) }}" class="hover:text-white">Events</a></li>
                    <li><a href="{{ route('community') }}" class="hover:text-white">Volunteer</a></li>
                </ul>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-white">About</h2>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a href="{{ route('about') }}" class="hover:text-white">Our mission</a></li>
                    <li><a href="{{ route('conduct') }}" class="hover:text-white">Code of conduct</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white">Contact</a></li>
                    <li><a href="{{ route('privacy') }}" class="hover:text-white">Privacy</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="container-page flex flex-col gap-2 py-6 text-xs text-slate-500 sm:flex-row sm:justify-between">
                <p>&copy; {{ date('Y') }} IT GURUs Germany. Information is provided by the community - always check official sources.</p>
                <p><a href="{{ config('itgurus.social.github') }}" class="hover:text-slate-300">Open source on GitHub</a></p>
            </div>
        </div>
    </footer>
</body>
</html>
