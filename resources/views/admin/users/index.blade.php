<x-layouts.admin title="Users & roles">
    <div class="mb-4 flex flex-wrap items-center gap-3">
        <form method="GET" class="w-full sm:w-72">
            @if ($unverifiedOnly)<input type="hidden" name="status" value="unverified">@endif
            <input type="search" name="q" value="{{ $q }}" placeholder="Search name or email…" class="input">
        </form>
        <a href="{{ route('admin.users.index', $unverifiedOnly ? [] : ['status' => 'unverified']) }}" class="btn-secondary btn-sm">
            {{ $unverifiedOnly ? 'Show everyone' : "Unverified only ({$unverifiedCount})" }}
        </a>
        @if ($staleUnverifiedCount > 0)
            <form method="POST" action="{{ route('admin.users.destroy-unverified') }}" onsubmit="return confirm('Delete {{ $staleUnverifiedCount }} unverified member account(s) older than 7 days? This cannot be undone.')">
                @csrf @method('DELETE')
                <button class="btn-secondary btn-sm text-red-700">Delete {{ $staleUnverifiedCount }} unverified (older than 7 days)</button>
            </form>
        @endif
    </div>
    @error('role')<p class="field-error mb-3">{{ $message }}</p>@enderror
    <div class="card overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr><th class="px-5 py-3">User</th><th class="px-5 py-3">Guides</th><th class="px-5 py-3">Joined</th><th class="px-5 py-3">Role</th><th class="px-5 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-5 py-3"><p class="font-medium">{{ $user->name }}</p><p class="text-xs text-slate-500">{{ $user->email }} @unless ($user->hasVerifiedEmail())<span class="text-amber-700">(unverified)</span>@endunless</p></td>
                        <td class="px-5 py-3">{{ $user->articles_count }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3">
                            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex items-center gap-2">
                                @csrf @method('PATCH')
                                <select name="role" class="input w-36 py-1.5" aria-label="Role for {{ $user->name }}">
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>
                                    @endforeach
                                </select>
                                <button class="btn-secondary btn-sm">Save</button>
                            </form>
                        </td>
                        <td class="px-5 py-3 text-right">
                            @if ($user->role === \App\Enums\Role::Member && ! $user->is(auth()->user()))
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button class="text-sm font-medium text-red-700 hover:text-red-800">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $users->links() }}</div>
    <p class="mt-4 text-xs text-slate-500"><strong>Member</strong>: writes guides (reviewed). <strong>Editor</strong>: reviews & publishes guides, manages news, topics and inbox. <strong>Administrator</strong>: everything, including roles.</p>
</x-layouts.admin>
