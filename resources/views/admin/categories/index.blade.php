<x-layouts.admin title="Topics">
    <div class="grid gap-6 xl:grid-cols-[1fr_340px]">
        <div class="card overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr><th class="px-5 py-3">Topic</th><th class="px-5 py-3">Guides</th><th class="px-5 py-3">Order</th><th class="px-5 py-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($categories as $category)
                        <tr @class(['bg-brand-50/50' => $editing?->is($category)])>
                            <td class="px-5 py-3"><div class="flex items-center gap-3"><x-icon :name="$category->icon ?? 'book'" class="size-5 text-brand-600" /><div><p class="font-medium">{{ $category->name }}</p><p class="text-xs text-slate-500">/{{ $category->slug }}</p></div></div></td>
                            <td class="px-5 py-3">{{ $category->articles_count }}</td>
                            <td class="px-5 py-3">{{ $category->sort_order }}</td>
                            <td class="whitespace-nowrap px-5 py-3 text-right">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn-secondary btn-sm">Edit</a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" data-confirm="Delete this topic?">@csrf @method('DELETE')<button class="btn btn-sm text-rose-600 hover:bg-rose-50">Delete</button></form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <form method="POST" action="{{ $editing ? route('admin.categories.update', $editing) : route('admin.categories.store') }}" class="card h-fit space-y-5 p-5">
            @csrf
            @if ($editing) @method('PUT') @endif
            <h2 class="font-semibold">{{ $editing ? 'Edit topic' : 'Add a topic' }}</h2>
            <x-form.input name="name" label="Name" :value="$editing?->name" required maxlength="100" />
            <x-form.input name="slug" label="URL slug" :value="$editing?->slug" hint="Leave empty to generate from the name." />
            <x-form.textarea name="description" label="Description" :value="$editing?->description" rows="3" maxlength="500" />
            <div class="grid grid-cols-2 gap-4">
                <x-form.select name="icon" label="Icon" :options="array_combine($icons, $icons)" :value="$editing?->icon" />
                <x-form.input name="sort_order" label="Order" type="number" min="0" :value="$editing?->sort_order ?? 100" />
            </div>
            <div class="flex gap-2">
                <button class="btn-primary flex-1">{{ $editing ? 'Save' : 'Add topic' }}</button>
                @if ($editing)<a href="{{ route('admin.categories.index') }}" class="btn-secondary">Cancel</a>@endif
            </div>
        </form>
    </div>
</x-layouts.admin>
