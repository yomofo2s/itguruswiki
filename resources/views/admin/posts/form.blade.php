@php($editing = $post->exists)
<x-layouts.admin :title="$editing ? 'Edit post' : 'New post'">
    <form method="POST" enctype="multipart/form-data" action="{{ $editing ? route('admin.posts.update', $post) : route('admin.posts.store') }}" class="grid gap-6 xl:grid-cols-[1fr_300px]">
        @csrf
        @if ($editing) @method('PUT') @endif
        <div class="card space-y-6 p-6">
            <x-form.input name="title" label="Title" :value="$post->title" required maxlength="200" />
            <x-form.textarea name="excerpt" label="Summary" :value="$post->excerpt" rows="2" maxlength="500" />
            <x-markdown-editor :value="$post->body" required />
        </div>
        <div class="space-y-6">
            <div class="card space-y-5 p-5">
                <x-form.select name="type" label="Type" :options="collect($types)->mapWithKeys(fn ($t) => [$t->value => $t->label()])" :value="$post->type?->value" />
                <x-form.input name="event_starts_at" label="Event date & time" type="datetime-local" :value="$post->event_starts_at?->format('Y-m-d\TH:i')" hint="Required for events." />
                <x-form.input name="event_location" label="Location" :value="$post->event_location" placeholder="Berlin or Online" />
                <x-form.input name="event_url" label="Registration link" type="url" :value="$post->event_url" />
            </div>
            <div class="card space-y-5 p-5">
                <x-form.input name="published_at" label="Publish at" type="datetime-local" :value="($post->published_at ?? ($editing ? null : now()))?->format('Y-m-d\TH:i')" hint="Leave empty to keep as draft. A future date schedules it." />
                <div>
                    <x-form.input name="cover" label="Cover image" type="file" accept="image/jpeg,image/png,image/webp" />
                    @if ($post->cover_path)
                        <label class="mt-3 flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" name="remove_cover" value="1" class="rounded border-slate-300"> Remove current image</label>
                    @endif
                </div>
                <button class="btn-primary w-full">{{ $editing ? 'Save changes' : 'Create post' }}</button>
            </div>
        </div>
    </form>
</x-layouts.admin>
