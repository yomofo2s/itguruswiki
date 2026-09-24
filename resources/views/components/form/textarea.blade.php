@props(['name', 'label' => null, 'value' => null, 'hint' => null, 'rows' => 4])
@php($error = $errors->first($name))
<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $name }}" class="label">{{ $label }}</label>
    @endif
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}"
        {{ $attributes->except('class')->class(['input', 'input-error' => $error]) }}
        @if ($error) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif>{{ old($name, $value) }}</textarea>
    @if ($hint && ! $error)
        <p class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @if ($error)
        <p id="{{ $name }}-error" class="field-error">{{ $error }}</p>
    @endif
</div>
