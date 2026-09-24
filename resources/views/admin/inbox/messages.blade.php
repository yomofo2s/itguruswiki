<x-layouts.admin title="Messages">
    <div class="card divide-y divide-slate-100">
        @forelse ($messages as $message)
            <a href="{{ route('admin.messages.show', $message) }}" class="flex items-start gap-4 px-5 py-4 hover:bg-slate-50">
                <span @class(['mt-2 size-2 shrink-0 rounded-full', 'bg-brand-500' => ! $message->read_at, 'bg-transparent' => $message->read_at])></span>
                <div class="min-w-0 flex-1">
                    <div class="flex justify-between gap-3"><p @class(['truncate', 'font-semibold' => ! $message->read_at])>{{ $message->subject }}</p><span class="shrink-0 text-xs text-slate-500">{{ $message->created_at->diffForHumans() }}</span></div>
                    <p class="truncate text-sm text-slate-500">{{ $message->name }} - {{ \Illuminate\Support\Str::limit($message->message, 100) }}</p>
                </div>
            </a>
        @empty
            <p class="px-5 py-12 text-center text-sm text-slate-500">No messages.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $messages->links() }}</div>
</x-layouts.admin>
