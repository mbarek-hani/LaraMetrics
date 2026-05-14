<x-app-layout>
    <x-slot name="titre">
        Plugins
    </x-slot>
    <div class="p-page">
        <div class="p-container p-container--lg">
            <h2 class="p-page__title">Plugins</h2>
            @if(empty($plugins))
                <x-card>
                    <div class="p-empty">
                        <x-custom-icon name="puzzle" class="p-empty__icon" />
                        <h3 class="p-empty__title">Aucun plugin détecté</h3>
                        <p class="p-empty__text">
                            Placez vos plugins dans le dossier <code class="p-code">/plugins</code>
                        </p>
                    </div>
                </x-card>
            @else
                <div class="p-grid p-grid--1 p-grid--2 p-grid--3 p-grid--gap-md">
                    @foreach($plugins as $plugin)
                        <x-card class="u-h-full">
                            <div class="u-flex-1">
                                <div class="p-row p-mb-1">
                                    <x-custom-icon name="puzzle" class="c-icon--sm u-text-gray-400" />
                                    <h3 class="p-section__title u-flex-1">
                                        {{ $plugin['nom'] }}
                                    </h3>
                                    <span class="p-text--xs u-text-gray-400">v{{ $plugin['version'] }}</span>
                                </div>

                                @if($plugin['description'])
                                    <p class="p-text p-mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.5rem;">
                                        {{ $plugin['description'] }}
                                    </p>
                                @endif

                                <div class="p-row p-mb-2">
                                    <span class="p-badge {{ $plugin['actif'] ? 'p-badge--success' : 'p-badge--neutral' }}">
                                        {{ $plugin['actif'] ? 'Actif' : 'Inactif' }}
                                    </span>
                                    <span class="p-text--xs">Par {{ $plugin['auteur'] }}</span>
                                </div>
                                
                                @if(!empty($plugin['hooks']))
                                    <div class="p-row p-mt-2">
                                        <x-custom-icon name="bolt" class="c-icon--xs u-text-gray-400" />
                                        <span class="p-text--xs u-break-all" style="color: var(--gray-400);">
                                            Hooks : {{ implode(', ', array_slice($plugin['hooks'], 0, 3)) }}{{ count($plugin['hooks']) > 3 ? '...' : '' }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <x-slot name="footer">
                                <div class="p-actions u-w-full u-justify-between">
                                    <x-button href="{{ route('plugins.show', $plugin['identifiant']) }}" size="sm" title="Voir les détails">
                                        <x-custom-icon name="eye" class="c-icon--xs" />
                                        <span class="u-hidden-mobile">Détails</span>
                                    </x-button>

                                    @if($plugin['actif'])
                                        <form method="POST" action="{{ route('plugins.desactiver', $plugin['identifiant']) }}">
                                            @csrf
                                            <x-button variant="danger" size="sm" type="submit" title="Désactiver ce plugin">
                                                <x-custom-icon name="stop" class="c-icon--xs" />
                                                <span class="u-hidden-mobile">Désactiver</span>
                                            </x-button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('plugins.activer', $plugin['identifiant']) }}">
                                            @csrf
                                            <x-button variant="primary" size="sm" type="submit" title="Activer ce plugin">
                                                <x-custom-icon name="play" class="c-icon--xs" />
                                                <span class="u-hidden-mobile">Activer</span>
                                            </x-button>
                                        </form>
                                    @endif
                                </div>
                            </x-slot>
                        </x-card>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>