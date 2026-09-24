<x-layouts.app title="Contact">
    <x-page-header title="Contact us" eyebrow="We're here to help">
        Questions, corrections, partnership ideas - send us a message.
    </x-page-header>
    <div class="container-page grid gap-10 py-12 lg:grid-cols-[1fr_1.4fr]">
        <div class="space-y-4">
            <div class="card flex gap-4 p-5">
                <x-icon name="mail" class="size-6 shrink-0 text-brand-600" />
                <div><h2 class="font-semibold">Email</h2><p class="mt-1 text-sm text-slate-600">{{ config('itgurus.contact_email') }}</p></div>
            </div>
            <div class="card flex gap-4 p-5">
                <x-icon name="shield" class="size-6 shrink-0 text-brand-600" />
                <div><h2 class="font-semibold">No legal advice</h2><p class="mt-1 text-sm text-slate-600">We are volunteers. For binding answers contact the responsible authority, a lawyer or a certified advisor.</p></div>
            </div>
        </div>
        <form method="POST" action="{{ route('contact.store') }}" class="card relative space-y-5 p-6 sm:p-8">
            @csrf
            <x-form.honeypot />
            <div class="grid gap-5 sm:grid-cols-2">
                <x-form.input name="name" label="Name" required autocomplete="name" :value="auth()->user()?->name" />
                <x-form.input name="email" label="Email" type="email" required autocomplete="email" :value="auth()->user()?->email" />
            </div>
            <x-form.input name="subject" label="Subject" required maxlength="200" :value="request('subject')" />
            <x-form.textarea name="message" label="Message" rows="6" required minlength="10" maxlength="5000" />
            <p class="text-xs text-slate-500">We only use your details to answer your message. See our <a href="{{ route('privacy') }}" class="underline">privacy notice</a>.</p>
            <button class="btn-primary">Send message</button>
        </form>
    </div>
</x-layouts.app>
