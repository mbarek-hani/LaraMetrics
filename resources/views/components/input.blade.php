@props([
    'label'       => null,
    'name'        => '',
    'type'        => 'text',
    'required'    => false,
    'placeholder' => '',
    'aide'        => null,
    'value'       => null,
])

<div>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
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
        {{ $attributes->merge(['class' => 'block w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500']) }}
    >

    @if($aide)
        <p class="mt-1 text-xs text-gray-400">{{ $aide }}</p>
    @endif

    @error($name)
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>
