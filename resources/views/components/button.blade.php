@props([
    'variant' => 'default',
    'size' => 'md',
    'href' => null,
])
@php
    $baseClass = 'c-btn';
    $variantClass = 'c-btn--' . ($variant ?? 'default');
    $sizeClass = 'c-btn--' . ($size ?? 'md');

    $finalClasses = "$baseClass $variantClass $sizeClass";
@endphp
@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $finalClasses]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $finalClasses]) }}>
        {{ $slot }}
    </button>
@endif