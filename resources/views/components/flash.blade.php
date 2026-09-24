@if (session('status'))
    <div role="status" {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-xl bg-brand-50 px-4 py-3 text-sm text-brand-900 ring-1 ring-brand-200']) }}>
        <x-icon name="check" class="mt-0.5 size-5 shrink-0 text-brand-600" />
        <p>{{ session('status') }}</p>
    </div>
@endif
@if ($errors->any() && ($showErrors ?? false))
    <div role="alert" class="mt-3 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-800 ring-1 ring-rose-200">
        <ul class="list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
