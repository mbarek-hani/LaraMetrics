@props([
    'titre' => null,
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'c-card']) }}>
    @if($titre)
        <div class="c-card__header">
            <h3 class="c-card__title">{{ $titre }}</h3>
        </div>
    @endif

    <div class="{{ $padding ? 'c-card__body--padded' : '' }}">
        {{ $slot }}
    </div>
</div>