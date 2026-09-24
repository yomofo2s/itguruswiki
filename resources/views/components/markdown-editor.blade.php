@props(['name' => 'body', 'value' => null, 'rows' => 18, 'label' => 'Content'])
@php($error = $errors->first($name))
<div data-markdown-editor data-preview-url="{{ route('dashboard.preview') }}">
    <div class="mb-1.5 flex items-end justify-between">
        <label for="{{ $name }}" class="label mb-0">{{ $label }}</label>
        <div class="flex rounded-lg bg-slate-100 p-0.5 text-xs font-medium" role="tablist">
            <button type="button" data-tab="write" aria-selected="true" class="rounded-md px-3 py-1 aria-selected:bg-white aria-selected:shadow-sm">Write</button>
            <button type="button" data-tab="preview" aria-selected="false" class="rounded-md px-3 py-1 aria-selected:bg-white aria-selected:shadow-sm">Preview</button>
        </div>
    </div>
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" {{ $attributes->class(['input font-mono text-[13px] leading-6', 'input-error' => $error]) }}>{{ old($name, $value) }}</textarea>
    <div data-preview hidden class="prose-content min-h-64 rounded-xl bg-white p-4 ring-1 ring-slate-200"></div>
    @if ($error)
        <p class="field-error">{{ $error }}</p>
    @else
        <p class="mt-1.5 text-xs text-slate-500">Markdown supported: <code>## Heading</code>, <code>**bold**</code>, <code>- list</code>, <code>1. steps</code>, <code>[link](https://…)</code>, tables.</p>
    @endif
</div>
