<x-layouts.auth title="Reset your password" subtitle="We'll email you a link to choose a new password.">
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <x-form.input name="email" label="Email" type="email" required autofocus autocomplete="username" />
        <button class="btn-primary w-full">Email reset link</button>
    </form>
    <x-slot:footer>
        <p class="mt-6 text-center text-sm text-slate-600"><a href="{{ route('login') }}" class="font-semibold text-brand-700">Back to log in</a></p>
    </x-slot:footer>
</x-layouts.auth>
