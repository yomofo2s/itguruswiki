@props(['name', 'label' => null, 'options' => [], 'value' => null, 'placeholder' => null])
@php($error = $errors->first($name))
@php($selected = (string) old($name, $value))
<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $name }}" class="label">{{ $label }}</label>
    @endif
    <select id="{{ $name }}" name="{{ $name }}" {{ $attributes->except('class')->class(['input', 'input-error' => $error]) }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $key => $text)
            <option value="{{ $key }}" @selected($selected === (string) $key)>{{ $text }}</option>
        @endforeach
    </select>
    @if ($error)
        <p class="field-error">{{ $error }}</p>
    @endif
</div>
