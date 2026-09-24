<x-layouts.app title="Privacy">
    <x-page-header title="Privacy" eyebrow="Legal" />
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
        <div class="prose-content">
            <p class="rounded-xl bg-amber-50 p-4 text-sm text-amber-900 ring-1 ring-amber-200">Template - have this page and an Impressum reviewed before going live. German law (DSGVO/GDPR, DDG) requires both.</p>
            <h2>What we store</h2>
            <ul>
                <li><strong>Account data:</strong> name, email address, password (hashed) and optional bio.</li>
                <li><strong>Content</strong> you submit, such as guides, contact messages and volunteer sign-ups.</li>
                <li><strong>Technical data:</strong> a session cookie that is needed for logging in, and IP addresses of contact form submissions for spam prevention.</li>
            </ul>
            <p>We use no tracking or advertising cookies, and fonts are served from our own server.</p>
            <h2>Hosting</h2>
            <p>The website is hosted by Hetzner Online GmbH in Germany.</p>
            <h2>Your rights</h2>
            <p>You can view and edit your data in your profile and delete your account at any time. For other requests (access, correction, deletion) contact <a href="mailto:{{ config('itgurus.contact_email') }}">{{ config('itgurus.contact_email') }}</a>.</p>
        </div>
    </div>
</x-layouts.app>
