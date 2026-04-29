@props(['route', 'label', 'icon'])

<a
    href="{{ route($route) }}"
    class="flex items-center gap-2.5 px-2 py-2 rounded text-sm font-medium transition
           {{ request()->routeIs($route . '*') || request()->routeIs($route)
               ? 'bg-gray-100 text-gray-900'
               : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
>
    <x-custom-icon :name="$icon" class="w-4 h-4 shrink-0" />
    {{ $label }}
</a>
