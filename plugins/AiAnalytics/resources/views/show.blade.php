<x-app-layout titre="Détails du rapport IA">
    <x-slot name="titre">
        Détails du rapport IA
    </x-slot>
    <div class="max-w-4xl mx-auto space-y-4">
        {{-- Actions --}}
        <div class="flex items-center my-4">
            <x-button href="{{ route('plugins.ai-analytics.historique.index') }}" class="flex items-center gap-1">
                <x-custom-icon name="arrow-left" class="w-4 h-4" />
                Retour à l'historique
            </x-button>
        </div>

        {{-- Score & Résumé --}}
        <x-card>
            <div class="flex items-start gap-6">
                <div class="shrink-0 flex flex-col items-center justify-center w-20 h-20 rounded bg-gray-50 border border-gray-200">
                    <span class="text-3xl font-bold {{ $rapport->score >= 7 ? 'text-green-700' : ($rapport->score >= 4 ? 'text-amber-600' : 'text-red-600') }}">
                        {{ $rapport->score }}
                    </span>
                    <span class="text-xs text-gray-400">/10</span>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <h1 class="text-lg font-bold text-gray-900">Analyse du site : {{ $rapport->site->nom }}</h1>
                        <span class="px-2 py-0.5 rounded text-xs font-medium border {{ $rapport->score >= 7 ? 'bg-green-50 text-green-700 border-green-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                            {{ $rapport->score >= 7 ? 'Performance Bonne' : 'Performance Moyenne' }}
                        </span>
                    </div>
                    <p class="text-gray-700 leading-relaxed">{{ $rapport->resume }}</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t text-xs text-gray-400 flex justify-between">
                <span>Période analysée : {{ $rapport->date_debut }} au {{ $rapport->date_fin }}</span>
                <span>Généré par {{ $rapport->fournisseur }} ({{ $rapport->modele }}) le {{ $rapport->created_at?->format('d/m/Y à H:i') }}</span>
            </div>
        </x-card>

        {{-- Points et Recommandations --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-card titre="Points clés">
                <ul class="space-y-3">
                    @foreach($rapport->points_cles as $point)
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <x-custom-icon name="check-circle" class="w-5 h-5 text-green-500 shrink-0" />
                            {{ $point }}
                        </li>
                    @endforeach
                </ul>
            </x-card>

            <x-card titre="Recommandations">
                <ul class="space-y-3">
                    @foreach($rapport->recommandations as $index => $reco)
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="bg-blue-50 text-blue-700 font-mono text-xs w-5 h-5 flex items-center justify-center rounded shrink-0">
                                {{ $index + 1 }}
                            </span>
                            {{ $reco }}
                        </li>
                    @endforeach
                </ul>
            </x-card>
        </div>

        {{-- Tendances --}}
        <x-card titre="Tendances observées">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($rapport->tendances as $tendance)
                    <div class="p-3 bg-gray-50 rounded border border-gray-100 text-sm text-gray-700 flex items-center gap-3">
                        <x-custom-icon name="arrow-trending-up" class="w-5 h-5 text-blue-500" />
                        {{ $tendance }}
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>
</x-app-layout>
