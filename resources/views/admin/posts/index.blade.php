<x-layouts.admin title="News & events">
    <x-slot:actions>
        <a href="{{ route('admin.posts.create') }}" class="btn-primary"><x-icon name="plus" class="size-4" /> New post</a>
    </x-slot:actions>
    <div class="card overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr><th class="px-5 py-3">Title</th><th class="px-5 py-3">Type</th><th class="px-5 py-3">Status</th><th class="px-5 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($posts as $post)
                    <tr>
                        <td class="px-5 py-3"><a href="{{ route('news.show', $post) }}" class="font-medium hover:text-brand-700">{{ $post->title }}</a>
                            @if ($post->isEvent() && $post->event_starts_at)<p class="text-xs text-slate-500">{{ $post->event_starts_at->format('d M Y H:i') }} · {{ $post->event_location }}</p>@endif</td>
                        <td class="px-5 py-3">{{ $post->type->label() }}</td>
                        <td class="px-5 py-3">
                            @if ($post->isPublished())<span class="badge bg-emerald-100 text-emerald-800">Published {{ $post->published_at->format('d M') }}</span>
                            @elseif ($post->published_at)<span class="badge bg-sky-100 text-sky-800">Scheduled {{ $post->published_at->format('d M H:i') }}</span>
                            @else<span class="badge bg-slate-100 text-slate-700">Draft</span>@endif
                        </td>
                        <td class="whitespace-nowrap px-5 py-3 text-right">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="inline" data-confirm="Delete this post?">@csrf @method('DELETE')<button class="btn btn-sm text-rose-600 hover:bg-rose-50">Delete</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-12 text-center text-slate-500">No posts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $posts->links() }}</div>
</x-layouts.admin>
