<x-layouts.auth title="Welcome back" subtitle="Log in to write guides and follow the community.">
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <x-form.input name="email" label="Email" type="email" required autofocus autocomplete="username" />
        <x-form.input name="password" label="Password" type="password" required autocomplete="current-password" />
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500"> Remember me
            </label>
            <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand-700 hover:text-brand-800">Forgot password?</a>
        </div>
        <button class="btn-primary w-full">Log in</button>
    </form>
    <x-slot:footer>
        <p class="mt-6 text-center text-sm text-slate-600">New here? <a href="{{ route('register') }}" class="font-semibold text-brand-700">Create an account</a></p>
    </x-slot:footer>
</x-layouts.auth>
