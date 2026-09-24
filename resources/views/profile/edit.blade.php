<x-layouts.app title="Profile">
    <div class="mx-auto max-w-3xl space-y-8 px-4 py-10 sm:px-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Your profile</h1>

        <form method="POST" action="{{ route('profile.update') }}" class="card space-y-5 p-6">
            @csrf @method('PATCH')
            <h2 class="font-semibold text-slate-900">Profile information</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-form.input name="name" label="Name" :value="$user->name" required autocomplete="name" />
                <x-form.input name="email" label="Email" type="email" :value="$user->email" required autocomplete="email" hint="Changing it requires verifying the new address." />
            </div>
            <x-form.textarea name="bio" label="About you (optional)" :value="$user->bio" rows="3" maxlength="1000" />
            <div class="flex items-center gap-3">
                <button class="btn-primary">Save</button>
                <span class="text-xs text-slate-500">Role: {{ $user->role->label() }}</span>
            </div>
        </form>

        <form method="POST" action="{{ route('profile.password') }}" class="card space-y-5 p-6">
            @csrf @method('PUT')
            <h2 class="font-semibold text-slate-900">Change password</h2>
            <x-form.input name="current_password" label="Current password" type="password" bag="password" required autocomplete="current-password" />
            <div class="grid gap-5 sm:grid-cols-2">
                <x-form.input name="password" label="New password" type="password" bag="password" required autocomplete="new-password" />
                <x-form.input name="password_confirmation" label="Confirm new password" type="password" bag="password" required autocomplete="new-password" />
            </div>
            <button class="btn-primary">Update password</button>
        </form>

        <form method="POST" action="{{ route('profile.destroy') }}" class="card space-y-4 p-6 ring-rose-200" data-confirm="Delete your account permanently? Your published guides stay online without your name.">
            @csrf @method('DELETE')
            <h2 class="font-semibold text-rose-700">Delete account</h2>
            <p class="text-sm text-slate-600">This removes your account permanently. Published guides remain but are no longer linked to you.</p>
            <x-form.input name="password" label="Confirm with your password" type="password" bag="deletion" required class="max-w-sm" />
            <button class="btn-danger">Delete my account</button>
        </form>
    </div>
</x-layouts.app>
