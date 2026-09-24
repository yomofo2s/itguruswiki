<x-layouts.admin :title="$message->subject">
    <div class="card p-6">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4 text-sm">
            <p><strong>{{ $message->name }}</strong> &lt;{{ $message->email }}&gt;</p>
            <p class="text-slate-500">{{ $message->created_at->format('d M Y H:i') }}</p>
        </div>
        <div class="whitespace-pre-line pt-4 text-slate-800">{{ $message->message }}</div>
        <div class="mt-6 flex gap-2">
            <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.$message->subject) }}" class="btn-primary"><x-icon name="mail" class="size-4" /> Reply by email</a>
            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" data-confirm="Delete this message?">@csrf @method('DELETE')<button class="btn-secondary">Delete</button></form>
        </div>
    </div>
</x-layouts.admin>
