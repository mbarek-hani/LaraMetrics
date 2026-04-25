@props([
    'titre' => '',
    'valeur' => '0',
    'icon'  => null,
])

<div class="bg-gray-50 border border-gray-200 rounded p-4">
    <div class="flex items-center gap-3">
        @if($icon)
            <div class="shrink-0">
                <x-icon :name="$icon" class="w-5 h-5 text-gray-500" />
            </div>
        @endif
        <div class="min-w-0">
            <p class="text-xs text-gray-500 font-medium truncate">{{ $titre }}</p>
            <p class="text-lg font-bold text-gray-900 mt-0.5">
                @if($valeur instanceof \Illuminate\View\ComponentSlot)
                    {{ $valeur }}
                @else
                    {{ $valeur }}
                @endif
            </p>
        </div>
    </div>
</div>
