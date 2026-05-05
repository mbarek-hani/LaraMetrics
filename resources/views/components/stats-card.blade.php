@props([
    'titre' => '',
    'valeur' => '0',
    'icon'  => null,
])

<div class="c-stats-card">
    <div class="c-stats-card__content">
        @if($icon)
            <div class="c-stats-card__icon-wrapper">
                <x-custom-icon :name="$icon" class="c-stats-card__icon" />
            </div>
        @endif
        <div class="c-stats-card__info">
            <p class="c-stats-card__title">{{ $titre }}</p>
            <p class="c-stats-card__value">
                @if($valeur instanceof \Illuminate\View\ComponentSlot)
                    {{ $valeur }}
                @else
                    {{ $valeur }}
                @endif
            </p>
        </div>
    </div>
</div>
