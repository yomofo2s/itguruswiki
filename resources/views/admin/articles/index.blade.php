<x-layouts.admin title="Guides">
    <x-slot:actions>
        <a href="{{ route('dashboard.articles.create') }}" class="btn-primary"><x-icon name="plus" class="size-4" /> New guide</a>
    </x-slot:actions>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            @foreach (\App\Enums\ArticleStatus::cases() as $case)
                <a href="{{ route('admin.articles.index', ['status' => $case->value]) }}"
                   @class(['rounded-full px-3 py-1 text-sm font-medium ring-1', 'bg-brand-700 text-white ring-brand-700' => $status === $case, 'bg-white text-slate-700 ring-slate-200 hover:bg-slate-50' => $status !== $case])>{{ $case->label() }}</a>
            @endforeach
            <a href="{{ route('admin.articles.index', ['status' => 'all']) }}" @class(['rounded-full px-3 py-1 text-sm font-medium ring-1', 'bg-brand-700 text-white ring-brand-700' => ! $status, 'bg-white text-slate-700 ring-slate-200' => $status])>All</a>
        </div>
        <form method="GET" class="w-full sm:w-64">
            <input type="hidden" name="status" value="{{ $status?->value ?? 'all' }}">
            <input type="search" name="q" value="{{ $q }}" placeholder="Search…" class="input">
        </form>
    </div>

    <div class="mt-6 space-y-4">
        @forelse ($articles as $article)
            <div class="card p-5">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                            <span class="badge {{ $article->status->badge() }}">{{ $article->status->label() }}</span>
                            @if ($article->featured)<span class="badge bg-gold-300/40 text-amber-900">Featured</span>@endif
                            <span>{{ $article->category->name }}</span>·
                            <span>{{ $article->author?->name ?? 'Deleted user' }}</span>·
                            <span>updated {{ $article->updated_at->diffForHumans() }}</span>
                        </div>
                        <a href="{{ route('guides.show', $article) }}" class="mt-1 block font-semibold text-slate-900 hover:text-brand-700">{{ $article->title }}</a>
                        @if ($article->excerpt)<p class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $article->excerpt }}</p>@endif
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('guides.show', $article) }}" class="btn-secondary btn-sm"><x-icon name="eye" class="size-4" /> View</a>
                        <a href="{{ route('dashboard.articles.edit', $article) }}" class="btn-secondary btn-sm"><x-icon name="pencil" class="size-4" /> Edit</a>
                        @if ($article->status !== \App\Enums\ArticleStatus::Published)
                            <form method="POST" action="{{ route('admin.articles.publish', $article) }}">@csrf<button class="btn-primary btn-sm"><x-icon name="check" class="size-4" /> Publish</button></form>
                        @else
                            <form method="POST" action="{{ route('admin.articles.feature', $article) }}">@csrf<button class="btn-secondary btn-sm"><x-icon name="star" class="size-4" /> {{ $article->featured ? 'Unfeature' : 'Feature' }}</button></form>
                            <form method="POST" action="{{ route('admin.articles.unpublish', $article) }}" data-confirm="Unpublish this guide?">@csrf<button class="btn-secondary btn-sm">Unpublish</button></form>
                        @endif
                        <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" data-confirm="Delete this guide permanently?">@csrf @method('DELETE')<button class="btn-sm btn text-rose-600 hover:bg-rose-50" title="Delete"><x-icon name="trash" class="size-4" /><span class="sr-only">Delete</span></button></form>
                    </div>
                </div>
                @if ($article->status === \App\Enums\ArticleStatus::Pending)
                    <details class="mt-4 rounded-xl bg-slate-50 p-3">
                        <summary class="cursor-pointer text-sm font-medium text-slate-700">Request changes…</summary>
                        <form method="POST" action="{{ route('admin.articles.reject', $article) }}" class="mt-3 flex flex-col gap-2 sm:flex-row">
                            @csrf
                            <input name="review_note" required maxlength="1000" placeholder="What should the author change? (sent by email)" class="input flex-1">
                            <button class="btn-danger btn-sm">Send feedback</button>
                        </form>
                    </details>
                @elseif ($article->review_note)
                    <p class="mt-3 text-sm text-rose-700"><strong>Feedback sent:</strong> {{ $article->review_note }}</p>
                @endif
            </div>
        @empty
            <div class="card px-6 py-16 text-center text-sm text-slate-500">No guides in this list.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $articles->links() }}</div>
</x-layouts.admin>
