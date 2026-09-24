<x-layouts.auth title="Choose a new password">
    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-form.input name="email" label="Email" type="email" required autocomplete="username" :value="$email" />
        <x-form.input name="password" label="New password" type="password" required autofocus autocomplete="new-password" />
        <x-form.input name="password_confirmation" label="Confirm password" type="password" required autocomplete="new-password" />
        <button class="btn-primary w-full">Reset password</button>
    </form>
</x-layouts.auth>
