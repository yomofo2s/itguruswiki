<x-layouts.app title="My guides">
    <div class="container-page py-10">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">My guides</h1>
                <p class="mt-1 text-sm text-slate-600">Drafts are private. Submitted guides are checked by an editor before they go live.</p>
            </div>
            <a href="{{ route('dashboard.articles.create') }}" class="btn-primary"><x-icon name="plus" class="size-4" /> New guide</a>
        </div>

        @if ($articles->isEmpty())
            <div class="card mt-8 grid place-items-center px-6 py-16 text-center">
                <x-icon name="pencil" class="size-10 text-slate-300" />
                <h2 class="mt-4 font-semibold text-slate-900">You haven't written a guide yet</h2>
                <p class="mt-1 max-w-md text-sm text-slate-600">Think about something you had to figure out the hard way - opening a bank account, your visa appointment, finding a job. Someone else needs that answer right now.</p>
                <a href="{{ route('dashboard.articles.create') }}" class="btn-primary mt-6">Write your first guide</a>
            </div>
        @else
            <div class="card mt-8 overflow-hidden">
                <ul class="divide-y divide-slate-100">
                    @foreach ($articles as $article)
                        <li class="flex flex-wrap items-center gap-4 p-4 sm:flex-nowrap">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="badge {{ $article->status->badge() }}">{{ $article->status->label() }}</span>
                                    <span class="text-xs text-slate-500">{{ $article->category->name }} · updated {{ $article->updated_at->diffForHumans() }}</span>
                                </div>
                                <a href="{{ route('guides.show', $article) }}" class="mt-1 block truncate font-medium text-slate-900 hover:text-brand-700">{{ $article->title }}</a>
                                @if ($article->status === \App\Enums\ArticleStatus::Rejected && $article->review_note)
                                    <p class="mt-2 rounded-lg bg-rose-50 px-3 py-2 text-sm text-rose-800"><strong>Editor feedback:</strong> {{ $article->review_note }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                @if ($article->isPublished())
                                    <span class="inline-flex items-center gap-1 text-xs text-slate-500"><x-icon name="eye" class="size-4" />{{ number_format($article->views) }}</span>
                                @endif
                                @can('update', $article)
                                    <a href="{{ route('dashboard.articles.edit', $article) }}" class="btn-secondary btn-sm">Edit</a>
                                @endcan
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="mt-6">{{ $articles->links() }}</div>
        @endif
    </div>
</x-layouts.app>
