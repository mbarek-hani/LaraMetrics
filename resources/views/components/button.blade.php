@props([
    'variant' => 'default',
    'size'    => 'md',
    'href'    => null,
])

@php
    $base = 'inline-flex items-center gap-1.5 font-medium rounded border transition focus:outline-none focus:ring-2 focus:ring-offset-1';

    $variants = [
        'primary' => 'bg-blue-500 hover:bg-blue-600 text-white border-blue-500 focus:ring-blue-300',
        'danger'  => 'bg-red-500 hover:bg-red-600 text-white border-red-500 focus:ring-red-300',
        'default' => 'bg-white hover:bg-gray-50 text-gray-700 border-gray-300 focus:ring-gray-300',
    ];

    $sizes = [
        'sm' => 'px-2.5 py-1 text-xs',
        'md' => 'px-3.5 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-sm',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['default']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
