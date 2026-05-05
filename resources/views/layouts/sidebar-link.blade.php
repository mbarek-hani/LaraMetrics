@props(['route', 'label', 'icon'])

<a
    href="{{ route($route) }}"
    class="c-sidebar-link {{ request()->routeIs($route . '*') || request()->routeIs($route) ? 'c-sidebar-link--active' : '' }}"
>
    <x-custom-icon :name="$icon" class="c-sidebar-link__icon c-icon--sm c-icon--no-shrink" />
    {{ $label }}
</a>
