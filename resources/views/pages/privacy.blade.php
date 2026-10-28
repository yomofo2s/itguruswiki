@php
    $legalName = config('itgurus.legal.name');
    $legalAddress = array_filter(array_map('trim', explode('|', (string) config('itgurus.legal.address'))));
    $email = config('itgurus.contact_email');
    $turnstile = filled(config('services.turnstile.site_key'));
    $google = filled(config('services.google.client_id'));
    $gtm = filled(config('itgurus.google_tag_manager_id'));
@endphp
<x-layouts.app title="Privacy">
    <x-page-header title="Privacy notice" eyebrow="Legal" />
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
        <div class="prose-content">
            <p class="text-sm text-slate-500">Information on the processing of personal data under Articles 13 and 14 of the General Data Protection Regulation (GDPR / DSGVO). Last updated: October 2026.</p>

            <h2>1. Who is responsible</h2>
            <p>The controller for this website is:</p>
            <p>
                <strong>{{ $legalName ?: '[Name of the operator – set ITG_LEGAL_NAME]' }}</strong><br>
                @forelse ($legalAddress as $line){{ $line }}<br>@empty [Address – set ITG_LEGAL_ADDRESS]<br>@endforelse
                Email: <a href="mailto:{{ $email }}">{{ $email }}</a>
            </p>
            <p>There is no data protection officer; please send privacy questions to the address above.</p>

            <h2>2. What we process, why, and on what legal basis</h2>

            <h3>Visiting the website (server logs)</h3>
            <p>When you open a page, our hosting provider's web server records your IP address, the date and time, the requested page and your browser's user agent. This is necessary to deliver the site securely and to detect abuse (Art. 6(1)(f) GDPR, legitimate interest in a secure and stable website). Log files are kept only for a short period and are not combined with other data.</p>

            <h3>Account and login</h3>
            <p>If you register we process your name, email address, a hashed password, an optional short bio, the role of your account and the time you confirmed your email. We use this to provide your account, let you submit guides and keep the platform safe (Art. 6(1)(b) GDPR). We send you a confirmation email and, on request, a password reset email. Accounts that are never confirmed may be deleted after about seven days.</p>
            <p>Your articles and posts stay on the site if you delete your account, but are no longer linked to your name.</p>
            @if ($google)
                <h3>Sign in with Google</h3>
                <p>You can sign in with your Google account. If you do, you are sent to Google, and Google tells us your name, your email address (only if Google has verified it) and a Google account ID. We use them to create or link your account (Art. 6(1)(b) GDPR). Google receives the information that you signed in to this website and processes it under its own privacy policy (<a href="https://policies.google.com/privacy" rel="noopener" target="_blank">policies.google.com/privacy</a>). If you do not want this, use email and password instead.</p>
            @endif

            <h3>Contact form</h3>
            <p>When you write to us we process the name, email address, subject and message you enter, and your IP address, which we store to prevent spam and abuse. We use this to answer you (Art. 6(1)(b) and (f) GDPR). We delete messages when they are no longer needed to handle your request.</p>

            <h3>Volunteer sign-up</h3>
            <p>If you sign up as a volunteer we process your name, email address, area of interest and any skills or message you add. We use them to contact you about volunteering (Art. 6(1)(a) and (b) GDPR). You can withdraw at any time by writing to us; we will then delete your entry.</p>

            <h3>Cookies</h3>
            <p>We only use cookies that are technically necessary: a session cookie and a security (CSRF) cookie, and, if you tick "Remember me" or sign in with Google, a login cookie that keeps you signed in on your device. They do not track you across websites and do not require consent (§ 25(2) no. 2 TDDDG; Art. 6(1)(f) GDPR). We use no advertising cookies, and fonts are served from our own server.</p>

            @if ($turnstile)
                <h3>Spam protection (Cloudflare Turnstile)</h3>
                <p>The contact, volunteer sign-up and registration forms use Cloudflare Turnstile to tell people from automated bots. When you open such a form, your browser loads a script from Cloudflare, and Cloudflare processes your IP address and technical information about your browser and device to decide whether you are a person. The provider is Cloudflare, Inc., 101 Townsend St., San Francisco, CA 94107, USA. We use it to protect the site and our team from spam and abuse (Art. 6(1)(f) GDPR). Where data is transferred to the USA, this is based on the EU–US Data Privacy Framework or standard contractual clauses. More information: <a href="https://www.cloudflare.com/privacypolicy/" rel="noopener" target="_blank">cloudflare.com/privacypolicy</a>.</p>
            @endif

            @if ($gtm)
                <h3>Google Tag Manager</h3>
                <p>This website uses Google Tag Manager, which loads scripts from Google. Google may process your IP address. Tag Manager itself sets no cookies, but the tags it loads may.</p>
            @endif

            <h2>3. Who receives your data</h2>
            <ul>
                <li><strong>Hosting:</strong> Hetzner Online GmbH, Industriestr. 25, 91710 Gunzenhausen, Germany, processes data on our behalf under a data processing agreement (Art. 28 GDPR). The servers are in Germany.</li>
                <li><strong>Email delivery:</strong> our email provider sends confirmation, password reset and notification emails on our behalf.</li>
                @if ($google)<li><strong>Google</strong>, if you use Google sign-in (see above).</li>@endif
                @if ($turnstile)<li><strong>Cloudflare</strong>, if you open a form protected by Turnstile (see above).</li>@endif
                <li>Members of our volunteer team who moderate content and answer messages can see the data you send us.</li>
            </ul>
            <p>We do not sell your data and do not use it for advertising or automated decision-making.</p>

            <h2>4. How long we keep data</h2>
            <p>We keep data only as long as needed for the purposes above or as the law requires. Account data is kept until you delete your account. Contact messages and volunteer entries are deleted when they are no longer needed. Server logs are deleted after a short period by our host.</p>

            <h2>5. Your rights</h2>
            <p>You have the right to:</p>
            <ul>
                <li>information about your data (Art. 15 GDPR),</li>
                <li>correction of wrong data (Art. 16),</li>
                <li>deletion (Art. 17),</li>
                <li>restriction of processing (Art. 18),</li>
                <li>receive your data in a common format (Art. 20),</li>
                <li>object to processing based on legitimate interest, on grounds relating to your situation (Art. 21), and</li>
                <li>withdraw consent at any time, with effect for the future (Art. 7(3)).</li>
            </ul>
            <p>You can view and edit your data in your profile and delete your account there. For anything else write to <a href="mailto:{{ $email }}">{{ $email }}</a>.</p>
            <p>You also have the right to lodge a complaint with a data protection supervisory authority, for example the authority of the German federal state in which you live or in which we are based. A list is available at <a href="https://www.bfdi.bund.de/DE/Service/Anschriften/Laender/Laender-node.html" rel="noopener" target="_blank">bfdi.bund.de</a>.</p>

            <h2>6. Security</h2>
            <p>This website uses TLS encryption (https). Passwords are stored only as hashes.</p>

            <h2>7. Changes</h2>
            <p>We update this notice when the website or the law changes. The current version is always on this page.</p>
        </div>
    </div>
</x-layouts.app>
