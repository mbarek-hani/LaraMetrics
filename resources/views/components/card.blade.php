@props([
    'titre' => null,
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'bg-gray-50 border border-gray-200 rounded']) }}>
    @if($titre)
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900">{{ $titre }}</h3>
        </div>
    @endif

    <div class="{{ $padding ? 'p-4' : '' }}">
        {{ $slot }}
    </div>
</div>
