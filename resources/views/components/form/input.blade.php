@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'hint' => null, 'bag' => 'default'])
@php($error = $errors->getBag($bag)->first($name))
<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $name }}" class="label">{{ $label }}</label>
    @endif
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
        @if ($type !== 'password' && $type !== 'file') value="{{ old($name, $value) }}" @endif
        {{ $attributes->except('class')->class(['input', 'input-error' => $error, 'file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-sm file:font-medium' => $type === 'file']) }}
        @if ($error) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif>
    @if ($hint && ! $error)
        <p class="mt-1.5 text-xs text-slate-500">{{ $hint }}</p>
    @endif
    @if ($error)
        <p id="{{ $name }}-error" class="field-error">{{ $error }}</p>
    @endif
</div>
