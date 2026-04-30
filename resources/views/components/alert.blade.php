@props(['type' => 'succes', 'message' => ''])

@php
    $classes = [
        'succes' => 'bg-green-50 border-green-200 text-green-700 border-l-4 border-l-green-500',
        'erreur' => 'bg-red-50 border-red-200 text-red-700 border-l-4 border-l-red-500',
    ][$type] ?? 'bg-blue-50 border-blue-200 text-blue-700 border-l-4 border-l-blue-500';
@endphp

<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 5000)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-x-8"
    x-transition:enter-end="opacity-100 transform translate-x-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-x-0"
    x-transition:leave-end="opacity-0 transform translate-x-8"
    class="fixed top-4 right-4 z-[100] w-full max-w-sm"
    x-cloak
>
    <div class="{{ $classes }} shadow-lg rounded-r p-4 flex items-start gap-3 border shadow-sm">
        <div class="flex-1">
            <p class="text-sm font-medium">
                {{ $message }}
            </p>
        </div>
        <button @click="show = false" class="text-current opacity-50 hover:opacity-100 transition">
            <x-custom-icon name="x-mark" class="w-4 h-4" />
        </button>
    </div>
</div>
