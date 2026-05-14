<x-app-layout>
    <x-slot name="titre">
        Site : {{ $site->nom }}
    </x-slot>

    <div class="p-page">
        <div class="p-container p-container--lg">
            {{-- En-tête --}}
            <div class="p-page__header p-mb-3">
                <div class="p-row">
                    <x-button href="{{ route('sites.index') }}" variant="default" size="sm" title="Retour à la liste">
                        <x-custom-icon name="arrow-left" class="c-icon--xs" />
                        <span class="u-hidden-mobile">Retour</span>
                    </x-button>
                    <h2 class="p-page__title u-mb-0">{{ $site->nom }}</h2>
                    <span class="p-badge {{ $site->actif ? 'p-badge--success' : 'p-badge--neutral' }}">
                        {{ $site->actif ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
                
                <div class="p-actions">
                    <form method="POST" action="{{ route('sites.toggle-actif', $site) }}">
                        @csrf
                        <x-button size="sm" type="submit" variant="{{ $site->actif ? 'default' : 'primary' }}">
                            <x-custom-icon name="{{ $site->actif ? 'stop' : 'play' }}" class="c-icon--xs" />
                            {{ $site->actif ? 'Désactiver' : 'Activer' }}
                        </x-button>
                    </form>
                    <form method="POST" action="{{ route('sites.destroy', $site) }}" onsubmit="return confirm('Supprimer ce site ?')">
                        @csrf
                        @method('DELETE')
                        <x-button size="sm" variant="danger" type="submit">
                            <x-custom-icon name="trash" class="c-icon--xs" />
                            <span class="u-hidden-mobile">Supprimer</span>
                        </x-button>
                    </form>
                </div>
            </div>

            <div class="p-grid p-grid--1 p-grid--2 p-grid--gap-md">
                {{-- Infos du site --}}
                <x-card titre="Informations Générales" class="u-h-full">
                    <div class="p-stack--sm">
                        <div class="p-row p-items-start">
                            <x-custom-icon name="globe" class="c-icon--sm u-text-gray-400 p-mt-1" />
                            <div>
                                <span class="p-info-label">Domaine principal</span>
                                <p class="p-info-value">{{ $site->domaine }}</p>
                            </div>
                        </div>
                        
                        <div class="p-row p-items-start">
                            <x-custom-icon name="calendar" class="c-icon--sm u-text-gray-400 p-mt-1" />
                            <div>
                                <span class="p-info-label">Date de création</span>
                                <p class="p-info-value">{{ $site->created_at->translatedFormat('d F Y à H:i') }}</p>
                            </div>
                        </div>

                        <div class="p-row p-items-start">
                            <x-custom-icon name="key" class="c-icon--sm u-text-gray-400 p-mt-1" />
                            <div class="u-flex-1">
                                <span class="p-info-label">Token de tracking</span>
                                <div class="p-row p-mt-1">
                                    <code class="p-info-value--mono u-flex-1">{{ $site->token_tracking }}</code>
                                    <button 
                                        x-data="{ copied: false }" 
                                        @click="navigator.clipboard.writeText('{{ $site->token_tracking }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="u-text-info u-text-xs u-font-bold"
                                        style="background: none; border: none; cursor: pointer;"
                                    >
                                        <span x-show="!copied">Copier</span>
                                        <span x-show="copied" x-cloak>Copié !</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-card>

                {{-- Statistiques rapides (Placeholder or actual data) --}}
                <x-card titre="Aperçu du trafic" class="u-h-full">
                    <div class="p-grid p-grid--2 p-grid--gap-md">
                        <div class="u-text-center">
                            <p class="u-text-gray-400 u-text-xs u-font-normal">Total Visites</p>
                            <p style="font-size: 2rem; font-weight: 700; color: var(--color-primary);">
                                {{ $site->visites_count ?? 0 }}
                            </p>
                        </div>
                        <div class="u-text-center">
                            <p class="u-text-gray-400 u-text-xs u-font-normal">Dernières 24h</p>
                            <p style="font-size: 2rem; font-weight: 700; color: var(--info-accent);">
                                {{ $site->visites_24h_count ?? 0 }}
                            </p>
                        </div>
                    </div>
                    <div class="p-mt-3 u-text-center">
                        <x-button href="{{ route('dashboard', ['site_id' => $site->id]) }}" variant="default" size="sm" class="u-w-full">
                            <x-custom-icon name="chart-bar" class="c-icon--xs" />
                            Voir le tableau de bord complet
                        </x-button>
                    </div>
                </x-card>

                {{-- Script de tracking (Full width) --}}
                <div class="p-grid--span-all" style="grid-column: 1 / -1;">
                    <x-card titre="Intégration du Script">
                        <div class="p-row p-mb-3 u-items-start">
                            <x-custom-icon name="information-circle" class="c-icon--sm u-text-info p-mt-1" />
                            <p class="p-text">
                                Pour commencer à collecter des données, copiez le code ci-dessous et collez-le dans la section <code class="p-code">&lt;head&gt;</code> de votre site web.
                            </p>
                        </div>

                        <div x-data="{ copie: false }" class="p-copy-wrapper">
                            <pre class="p-code-block"><code x-ref="code">{{ $site->getScriptTracking() }}</code></pre>

                            <button @click="
                                    navigator.clipboard.writeText($refs.code.textContent);
                                    copie = true;
                                    setTimeout(() => copie = false, 2000);
                                "
                                class="p-copy-btn"
                                title="Copier le script"
                            >
                                <span x-show="!copie" x-cloak>
                                    <x-custom-icon name="clipboard" class="c-icon--sm" />
                                </span>

                                <span x-show="copie" x-cloak>
                                    <x-custom-icon name="check" class="c-icon--sm u-text-success" />
                                </span>
                            </button>
                        </div>
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
