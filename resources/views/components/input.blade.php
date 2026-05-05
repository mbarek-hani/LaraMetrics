@props([
    'label'       => null,
    'name'        => '',
    'type'        => 'text',
    'required'    => false,
    'placeholder' => '',
    'aide'        => null,
    'value'       => null,
])

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
        {{ $attributes->merge(['class' => 'c-input']) }}
    >

    @if($aide)
        <p class="c-input-help">{{ $aide }}</p>
    @endif

    @error($name)
        <p class="c-input-error">{{ $message }}</p>
    @enderror
</div>
