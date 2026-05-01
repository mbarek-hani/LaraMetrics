@props(['type' => 'succes', 'message' => ''])

@php
    $modifier = match ($type) {
        'succes' => 'c-alert--success',
        'erreur' => 'c-alert--erreur',
        default => 'c-alert--info',
    };
@endphp

<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-8"
    x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-x-0"
    x-transition:leave-end="opacity-0 transform translate-x-8" class="c-alert-wrapper" x-cloak>

    <div class="c-alert {{ $modifier }}">
        <div class="c-alert__message">
            <p>{{ $message }}</p>
        </div>

        <button @click="show = false" class="c-alert__close">
            <x-custom-icon name="x-mark" style="width: 1rem; height: 1rem;" />
        </button>
    </div>
</div>