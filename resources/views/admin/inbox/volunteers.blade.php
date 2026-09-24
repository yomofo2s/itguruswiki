<x-layouts.admin title="Volunteers">
    <div class="card overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr><th class="px-5 py-3">Person</th><th class="px-5 py-3">Interest</th><th class="px-5 py-3">Details</th><th class="px-5 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($volunteers as $volunteer)
                    <tr class="align-top">
                        <td class="px-5 py-3"><p class="font-medium">{{ $volunteer->name }}</p><a href="mailto:{{ $volunteer->email }}" class="text-xs text-brand-700">{{ $volunteer->email }}</a><p class="text-xs text-slate-500">{{ $volunteer->created_at->format('d M Y') }}</p></td>
                        <td class="px-5 py-3">{{ $volunteer->interestLabel() }}</td>
                        <td class="max-w-sm px-5 py-3 text-slate-600">{{ $volunteer->skills }}@if ($volunteer->message)<p class="mt-1 text-xs">{{ $volunteer->message }}</p>@endif</td>
                        <td class="whitespace-nowrap px-5 py-3 text-right">
                            <form method="POST" action="{{ route('admin.volunteers.contacted', $volunteer) }}" class="inline">@csrf
                                <button @class(['btn btn-sm', 'bg-emerald-100 text-emerald-800' => $volunteer->contacted_at, 'btn-secondary' => ! $volunteer->contacted_at])>{{ $volunteer->contacted_at ? 'Contacted' : 'Mark contacted' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.volunteers.destroy', $volunteer) }}" class="inline" data-confirm="Remove this volunteer?">@csrf @method('DELETE')<button class="btn btn-sm text-rose-600 hover:bg-rose-50">Remove</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-12 text-center text-slate-500">No volunteers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $volunteers->links() }}</div>
</x-layouts.admin>
