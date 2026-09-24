<x-layouts.app title="About us">
    <x-page-header title="Help make our life better with good news." eyebrow="Our mission">
        IT GURUs Germany is a volunteer community making reliable information easy to find for Nigerians and Africans who live in - or are moving to - Germany.
    </x-page-header>

    <div class="container-page grid gap-12 py-14 lg:grid-cols-[2fr_1fr]">
        <div class="prose-content">
            <h2>Why we exist</h2>
            <p>Every day, useful answers pass through WhatsApp and social media groups: how to open a blocked account, which documents family reunion needs, how to get a qualification recognised, where to find a flat. A week later they are buried under new messages - and the same questions are asked again.</p>
            <p>We are building one place where that knowledge is <strong>collected, verified and kept up to date</strong>, whether it is about studying, working, family reunion or everyday life in Germany.</p>

            <h2>How it works</h2>
            <ol>
                <li><strong>Members share</strong> what they have learned as step-by-step guides.</li>
                <li><strong>Editors review</strong> every guide against official sources before it is published.</li>
                <li><strong>The community keeps it fresh</strong> - anyone can report outdated information.</li>
            </ol>

            <h2>Get involved</h2>
            <p>You don't need to be an expert. Share your experience, help us verify guides, organise a meetup or help build this website - it is open source and we welcome developers of all levels. We don't bite :)</p>
        </div>
        <aside class="space-y-4">
            @foreach ([
                ['shield', 'Verified', 'Guides are reviewed by editors and link to official sources.'],
                ['family', 'By the community', 'Written by people who have been through the process themselves.'],
                ['heart', 'Free & open', 'No ads, no paywall. The code is open source on GitHub.'],
            ] as [$icon, $title, $text])
                <div class="card flex gap-4 p-5">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-brand-50 text-brand-700"><x-icon :name="$icon === 'heart' ? 'health' : $icon" /></span>
                    <div><h3 class="font-semibold text-slate-900">{{ $title }}</h3><p class="mt-1 text-sm text-slate-600">{{ $text }}</p></div>
                </div>
            @endforeach
            <div class="flex gap-3 pt-2">
                <a href="{{ route('community') }}" class="btn-primary flex-1">Volunteer</a>
                <a href="{{ route('contact') }}" class="btn-secondary flex-1">Contact us</a>
            </div>
        </aside>
    </div>
</x-layouts.app>
