@props([
    'label'       => null,
    'name'        => '',
    'type'        => 'text',
    'required'    => false,
    'placeholder' => '',
    'aide'        => null,
    'value'       => null,
])

@php
    $errorKey = str_replace(['[', ']', '..'], ['.', '', '.'], $name);
    $errorKey = rtrim($errorKey, '.');
@endphp

<div class="c-input-group">
    @if($label)
        <label for="{{ $name }}" class="c-input-label">
            {{ $label }}
            @if($required)
                <span class="c-input-required">*</span>
            @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        placeholder="{{ $placeholder }}"
        value="{{ $value }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'c-input' . ($errors->has($errorKey) ? ' c-input--error' : '')]) }}
    >

    @if($aide)
        <p class="c-input-help">{{ $aide }}</p>
    @endif

    @error($errorKey)
        <p class="c-input-error">{{ $message }}</p>
    @enderror
</div>
