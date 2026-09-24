<x-layouts.app title="Code of conduct">
    <x-page-header title="Code of conduct" eyebrow="Community">
        We want this to be a welcoming, harassment-free space for everyone.
    </x-page-header>
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
        <div class="prose-content">
            <h2>Our pledge</h2>
            <p>We pledge to make participation in our community a harassment-free experience for everyone, regardless of age, body size, visible or invisible disability, ethnicity, sex characteristics, gender identity and expression, level of experience, education, socio-economic status, nationality, personal appearance, race, religion, or sexual identity and orientation.</p>

            <h2>Expected behaviour</h2>
            <ul>
                <li>Be kind and respectful, and assume good intentions.</li>
                <li>Share accurate information and cite official sources where you can.</li>
                <li>Give and gracefully accept constructive feedback.</li>
                <li>Protect privacy - never post other people's personal data or documents.</li>
            </ul>

            <h2>Not acceptable</h2>
            <ul>
                <li>Harassment, insults, discriminatory or sexualised language.</li>
                <li>Deliberately false or misleading information, scams, or advertising.</li>
                <li>Publishing others' private information without explicit permission.</li>
            </ul>

            <h2>Enforcement</h2>
            <p>Editors and administrators may remove content and suspend accounts that violate this code. Report problems to <a href="mailto:{{ config('itgurus.contact_email') }}">{{ config('itgurus.contact_email') }}</a> or via the <a href="{{ route('contact') }}">contact form</a>. All reports are handled confidentially.</p>
            <p class="text-sm text-slate-500">Adapted from the <a href="https://www.contributor-covenant.org" rel="noopener">Contributor Covenant</a>, version 2.1.</p>
        </div>
    </div>
</x-layouts.app>
