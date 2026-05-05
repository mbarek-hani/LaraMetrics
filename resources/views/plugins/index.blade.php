<x-app-layout>
    <x-slot name="titre">
        Plugins
    </x-slot>
    <div class="p-page">
        <div class="p-container p-container--md">
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
                <div class="p-stack--sm">
                    @foreach($plugins as $plugin)
                        <x-card>
                            <div class="p-list-item p-list-item--top">
                                <div class="p-row" style="align-items: flex-start; gap: 0.75rem;">
                                    <div class="p-mt-sm">
                                        <x-custom-icon name="puzzle" class="c-icon--md c-icon--gray-400" />
                                    </div>
                                    <div>
                                        <div class="p-row">
                                            <h3 class="p-section__title">
                                                {{ $plugin['nom'] }}
                                            </h3>
                                            <span class="p-text--xs">v{{ $plugin['version'] }}</span>
                                            @if($plugin['actif'])
                                                <span class="p-badge p-badge--success">
                                                    Actif
                                                </span>
                                            @else
                                                <span class="p-badge p-badge--neutral">
                                                    Inactif
                                                </span>
                                            @endif
                                        </div>

                                        @if($plugin['description'])
                                            <p class="p-text p-mt-1">{{ $plugin['description'] }}</p>
                                        @endif

                                        <p class="p-text--xs p-mt-1">
                                            Par {{ $plugin['auteur'] }}
                                        </p>

                                        @if(!empty($plugin['hooks']))
                                            <div class="p-row p-mt-2">
                                                <x-custom-icon name="bolt" class="c-icon--xs c-icon--gray-300" />
                                                <span class="p-text--xs">
                                                    Hooks : {{ implode(', ', $plugin['hooks']) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-actions p-indent">
                                    <x-button href="{{ route('plugins.show', $plugin['identifiant']) }}" size="sm">
                                        <x-custom-icon name="eye" class="c-icon--xs" />
                                        Détails
                                    </x-button>

                                    @if($plugin['actif'])
                                        <form method="POST" action="{{ route('plugins.desactiver', $plugin['identifiant']) }}">
                                            @csrf
                                            <x-button variant="danger" size="sm" type="submit">
                                                <x-custom-icon name="stop" class="c-icon--xs" />
                                                Désactiver
                                            </x-button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('plugins.activer', $plugin['identifiant']) }}">
                                            @csrf
                                            <x-button variant="primary" size="sm" type="submit">
                                                <x-custom-icon name="play" class="c-icon--xs" />
                                                Activer
                                            </x-button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </x-card>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>