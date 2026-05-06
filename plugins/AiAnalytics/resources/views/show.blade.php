<x-app-layout titre="Détails du rapport IA">
    <x-slot name="titre">
        Détails du rapport IA
    </x-slot>
    <div class="p-ai p-container p-container--md">
        {{-- Actions --}}
        <div class="p-actions p-mt-2 p-mb-2">
            <x-button href="{{ route('plugins.ai-analytics.historique.index') }}" size="sm">
                <x-custom-icon name="arrow-left" class="c-icon--sm" />
                Retour à l'historique
            </x-button>
        </div>

        {{-- Score & Résumé --}}
        <x-card>
            <div class="p-ai__report-layout">
                <div class="p-ai__score-circle p-ai__score-circle--lg">
                    <span class="p-ai__score-value"
                        style="color: {{ $rapport->score >= 7 ? 'var(--success-text)' : ($rapport->score >= 4 ? 'var(--warning-text)' : 'var(--error-text)') }}">
                        {{ $rapport->score }}
                    </span>
                    <span class="p-text--xs">/10</span>
                </div>
                <div class="p-ai__report-content">
                    <div class="p-row p-mb-2 p-row--wrap">
                        <h1 class="p-section__title" style="font-size: 1.125rem;">Analyse du site :
                            {{ $rapport->site->nom }}
                        </h1>
                        <span
                            class="p-badge {{ $rapport->score >= 7 ? 'p-badge--success' : ($rapport->score >= 4 ? 'p-badge--warning' : 'p-badge--error') }}">
                            {{ $rapport->score >= 7 ? 'Performance Bonne' : ($rapport->score >= 4 ? 'Performance Moyenne' : 'Performance Faible') }}
                        </span>
                    </div>
                    <p class="p-text p-text--main">{{ $rapport->resume }}</p>
                </div>
            </div>
            <div class="p-ai__report-meta">
                <span>Période analysée : {{ $rapport->date_debut }} au {{ $rapport->date_fin }}</span>
                <span>Généré par {{ $rapport->fournisseur }} ({{ $rapport->modele }}) le
                    {{ $rapport->created_at?->format('d/m/Y à H:i') }}</span>
            </div>
        </x-card>

        {{-- Points et Recommandations --}}
        <div class="p-grid p-grid--2 p-grid--gap-md">
            <x-card titre="Points clés">
                <ul class="p-ai__list">
                    @foreach($rapport->points_cles as $point)
                        <li class="p-ai__list-item">
                            <x-custom-icon name="check-circle" class="c-icon--lg c-icon--success" />
                            {{ $point }}
                        </li>
                    @endforeach
                </ul>
            </x-card>

            <x-card titre="Recommandations">
                <ul class="p-ai__list">
                    @foreach($rapport->recommandations as $index => $reco)
                        <li class="p-ai__list-item">
                            <span class="p-ai__reco-num">
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
            <div class="p-grid p-grid--2 p-grid--gap-md">
                @foreach($rapport->tendances as $tendance)
                    <div class="p-ai__trend-card">
                        <x-custom-icon name="arrow-trending-up" class="c-icon--lg c-icon--info" />
                        {{ $tendance }}
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>
    </div>
    </div>
</x-app-layout>