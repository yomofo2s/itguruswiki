<x-layouts.auth title="Join IT GURUs Germany" subtitle="Free for everyone. Share guides and help the community.">
    <form method="POST" action="{{ route('register') }}" class="relative space-y-5">
        @csrf
        <x-form.honeypot />
        <x-form.input name="name" label="Name" required autofocus autocomplete="name" />
        <x-form.input name="email" label="Email" type="email" required autocomplete="username" />
        <x-form.input name="password" label="Password" type="password" required autocomplete="new-password" hint="At least 10 characters with letters and numbers." />
        <x-form.input name="password_confirmation" label="Confirm password" type="password" required autocomplete="new-password" />
        <div>
            <label class="flex items-start gap-2 text-sm text-slate-600">
                <input type="checkbox" name="terms" value="1" required class="mt-0.5 rounded border-slate-300 text-brand-600 focus:ring-brand-500" @checked(old('terms'))>
                <span>I accept the <a href="{{ route('conduct') }}" class="font-medium text-brand-700 underline" target="_blank">code of conduct</a> and <a href="{{ route('privacy') }}" class="font-medium text-brand-700 underline" target="_blank">privacy notice</a>.</span>
            </label>
            @error('terms')<p class="field-error">{{ $message }}</p>@enderror
        </div>
        <button class="btn-primary w-full">Create account</button>
    </form>
    <x-slot:footer>
        <p class="mt-6 text-center text-sm text-slate-600">Already a member? <a href="{{ route('login') }}" class="font-semibold text-brand-700">Log in</a></p>
    </x-slot:footer>
</x-layouts.auth>
