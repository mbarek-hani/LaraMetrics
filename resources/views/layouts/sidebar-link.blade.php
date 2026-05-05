@props(['route', 'label', 'icon'])

<a
    href="{{ route($route) }}"
    class="c-sidebar-link {{ request()->routeIs($route . '*') || request()->routeIs($route) ? 'c-sidebar-link--active' : '' }}"
>
    <x-custom-icon :name="$icon" class="c-sidebar-link__icon w-4 h-4 shrink-0" />
    {{ $label }}
</a>
