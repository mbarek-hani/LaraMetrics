@props(['type' => 'succes', 'message' => ''])

@php
    $modifier = match ($type) {
        'succes', 'success' => 'c-alert--success',
        'erreur', 'error', 'danger' => 'c-alert--erreur',
        'warning' => 'c-alert--warning',
        default => 'c-alert--info',
    };
@endphp

<div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
    x-transition:enter="c-alert-enter" 
    x-transition:enter-start="c-alert-enter-start"
    x-transition:enter-end="c-alert-enter-end" 
    x-transition:leave="c-alert-leave"
    x-transition:leave-start="c-alert-leave-start"
    x-transition:leave-end="c-alert-leave-end" 
    class="c-alert-wrapper" x-cloak>

    <div class="c-alert {{ $modifier }}">
        <div class="c-alert__icon">
            @if(in_array($type, ['succes', 'success']))
                <x-custom-icon name="check-circle" class="c-icon--sm" />
            @elseif(in_array($type, ['erreur', 'error', 'danger']))
                <x-custom-icon name="exclamation-circle" class="c-icon--sm" />
            @elseif($type === 'warning')
                <x-custom-icon name="exclamation-triangle" class="c-icon--sm" />
            @else
                <x-custom-icon name="information-circle" class="c-icon--sm" />
            @endif
        </div>
        
        <div class="c-alert__message">
            @if($message)
                <p>{{ $message }}</p>
            @endif
            {{ $slot }}
        </div>

        <button @click="show = false" class="c-alert__close">
            <x-custom-icon name="x-mark" class="c-icon--sm" />
        </button>
    </div>
</div>