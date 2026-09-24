<x-layouts.app title="Community">
    <x-page-header title="Join the community" eyebrow="Get involved">
        Meet people who have walked the same path, ask questions and help others find their way.
    </x-page-header>

    <div class="container-page py-12">
        <div class="grid gap-6 md:grid-cols-3">
            @foreach ([
                ['pencil', 'Share a guide', 'Turn your experience into a guide that helps the next person.', auth()->check() ? route('dashboard.articles.create') : route('register'), 'Start writing'],
                ['chat', 'Ask a question', 'Missing something? Send us your question and we will research it.', route('contact'), 'Ask us'],
                ['calendar', 'Come to an event', 'Meetups and online sessions about careers, studies and more.', route('news.index', ['type' => 'event']), 'See events'],
            ] as [$icon, $title, $text, $url, $cta])
                <div class="card flex flex-col p-6">
                    <span class="grid size-11 place-items-center rounded-xl bg-brand-50 text-brand-700"><x-icon :name="$icon" class="size-6" /></span>
                    <h2 class="mt-4 font-semibold text-slate-900">{{ $title }}</h2>
                    <p class="mt-1 flex-1 text-sm text-slate-600">{{ $text }}</p>
                    <a href="{{ $url }}" class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-700">{{ $cta }} <x-icon name="arrow-right" class="size-4" /></a>
                </div>
            @endforeach
        </div>

        @if (array_filter(config('itgurus.social')))
            <div class="mt-12">
                <h2 class="text-xl font-bold text-slate-900">Find us online</h2>
                <div class="mt-4 flex flex-wrap gap-3">
                    @foreach (array_filter(config('itgurus.social')) as $network => $url)
                        <a href="{{ $url }}" rel="noopener" target="_blank" class="btn-secondary capitalize"><x-icon name="globe" class="size-4" /> {{ $network }}</a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-16 grid gap-10 lg:grid-cols-[1fr_1.2fr]" id="volunteer">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900">Become a volunteer</h2>
                <p class="mt-3 text-slate-600">Everything here is run by volunteers. An hour a month already makes a difference.</p>
                <ul class="mt-6 space-y-3 text-sm text-slate-700">
                    @foreach ($interests as $label)
                        <li class="flex items-center gap-2"><x-icon name="check" class="size-4 text-brand-600" /> {{ $label }}</li>
                    @endforeach
                </ul>
                @if ($events->isNotEmpty())
                    <h3 class="mt-10 font-semibold text-slate-900">Upcoming events</h3>
                    <div class="card mt-3 divide-y divide-slate-100 p-2">
                        @foreach ($events as $event)
                            <x-event-item :event="$event" />
                        @endforeach
                    </div>
                @endif
            </div>
            <form method="POST" action="{{ route('volunteer.store') }}" class="card relative space-y-5 p-6 sm:p-8">
                @csrf
                <x-form.honeypot />
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-form.input name="name" label="Name" required autocomplete="name" :value="auth()->user()?->name" />
                    <x-form.input name="email" label="Email" type="email" required autocomplete="email" :value="auth()->user()?->email" />
                </div>
                <x-form.select name="interest" label="How would you like to help?" :options="$interests" placeholder="Choose one…" required />
                <x-form.input name="skills" label="Skills or experience (optional)" maxlength="500" placeholder="e.g. Laravel developer, studied in Aachen, HR recruiter" />
                <x-form.textarea name="message" label="Anything else? (optional)" rows="3" maxlength="2000" />
                <button class="btn-primary w-full sm:w-auto">Sign me up</button>
            </form>
        </div>
    </div>
</x-layouts.app>
