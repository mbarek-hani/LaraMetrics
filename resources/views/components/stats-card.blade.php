@props([
    'titre'  => 'Titre',
    'valeur' => '0',
    'icone'  => null,
    'couleur' => 'indigo',
])

<div class="bg-white overflow-hidden shadow-sm rounded-lg">
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 truncate">
                    {{ $titre }}
                </p>
                <p class="mt-1 text-3xl font-semibold text-gray-900">
                    {{ $valeur }}
                </p>
            </div>

            @if($icone)
                <div class="flex-shrink-0 p-3 rounded-full bg-{{ $couleur }}-100">
                    <svg class="w-6 h-6 text-{{ $couleur }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($icone === 'users')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>

                        @elseif($icone === 'eye')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>

                        @elseif($icone === 'chart-bar')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>

                        @elseif($icone === 'arrow-trending-down')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                        @endif
                    </svg>
                </div>
            @endif
        </div>

        {{-- Slot optionnel pour contenu supplémentaire (ex: sparkline, tendance) --}}
        @if(isset($footer))
            <div class="mt-4 border-t border-gray-100 pt-3">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
