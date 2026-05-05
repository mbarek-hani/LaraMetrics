@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'c-dropdown__menu'])

@php
$alignmentClasses = match ($align) {
    'left' => 'c-dropdown__content--align-left',
    'top' => 'c-dropdown__content--align-top',
    default => 'c-dropdown__content--align-right',
};

$width = match ($width) {
    '48' => 'c-dropdown__content--w48',
    default => $width,
};
@endphp

<div class="c-dropdown" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="c-transition-fade-enter"
            x-transition:enter-start="c-transition-fade-enter-start"
            x-transition:enter-end="c-transition-fade-enter-end"
            x-transition:leave="c-transition-fade-leave"
            x-transition:leave-start="c-transition-fade-leave-start"
            x-transition:leave-end="c-transition-fade-leave-end"
            class="c-dropdown__content {{ $width }} {{ $alignmentClasses }}"
            style="display: none;"
            @click="open = false">
        <div class="{{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
