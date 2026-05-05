@props([
    'name'  => 'question-mark-circle',
    'class' => 'c-icon',
])

@php
    $aliases = [
        'users'               => 'heroicon-o-users',
        'eye'                 => 'heroicon-o-eye',
        'arrow-trending-down' => 'heroicon-o-arrow-trending-down',
        'clock'               => 'heroicon-o-clock',
        'chart-bar'           => 'heroicon-o-chart-bar',
        'globe'               => 'heroicon-o-globe-alt',
        'document'            => 'heroicon-o-document-text',
        'link'                => 'heroicon-o-link',
        'device-phone'        => 'heroicon-o-device-phone-mobile',
        'computer'            => 'heroicon-o-computer-desktop',
        'plus'                => 'heroicon-o-plus',
        'trash'               => 'heroicon-o-trash',
        'cog'                 => 'heroicon-o-cog-6-tooth',
        'code'                => 'heroicon-o-code-bracket',
        'clipboard'           => 'heroicon-o-clipboard-document',
        'cpu'                 => 'heroicon-o-cpu-chip',
        'arrow-path'          => 'heroicon-o-arrow-path',
        'check'               => 'heroicon-o-check',
        'x-mark'              => 'heroicon-o-x-mark',
        'puzzle'              => 'heroicon-o-puzzle-piece',
        'play'                => 'heroicon-o-play',
        'stop'                => 'heroicon-o-stop',
        'bolt'                => 'heroicon-o-bolt',
        'cursor-click'        => 'heroicon-o-cursor-arrow-rays',
        'bars-3'              => 'heroicon-o-bars-3',
        'chevron-down'        => 'heroicon-o-chevron-down',
        'chevron-left'        => 'heroicon-o-chevron-left',
        'chevron-right'       => 'heroicon-o-chevron-right',
        'information-circle'  => 'heroicon-o-information-circle',
        'exclamation'         => 'heroicon-o-exclamation-triangle',
        'funnel'              => 'heroicon-o-funnel',
        'tag'                 => 'heroicon-o-tag',
    ];

    if (str_starts_with($name, 'heroicon-')) {
        $icon = $name;
    } else {
        $icon = $aliases[$name] ?? 'heroicon-o-' . $name;
    }
@endphp

@svg($icon, $class)
