<x-app-layout>
    <x-slot name="titre">
        Sites
    </x-slot>
    <div class="p-page">
        <div class="p-container p-container--md">
            <div class="p-page__header">
                <h2 class="p-page__title" style="margin-bottom: 0;">Sites</h2>
                <x-button variant="primary" href="{{ route('sites.create') }}">
                    <x-custom-icon name="plus" class="w-4 h-4" />
                    Ajouter un site
                </x-button>
            </div>
            @if($sites->isEmpty())
                <x-card>
                    <div class="p-empty">
                        <x-custom-icon name="globe" class="p-empty__icon" />
                        <p class="p-empty__text">Aucun site pour le moment.</p>
                    </div>
                </x-card>
            @else
                <div class="p-stack--sm">
                    @foreach($sites as $site)
                        <x-card>
                            <div class="p-list-item">
                                <div>
                                    <div class="p-row">
                                        <div
                                            class="p-status-dot {{ $site->actif ? 'p-status-dot--active' : 'p-status-dot--inactive' }}">
                                        </div>
                                        <h3 class="p-section__title">{{ $site->nom }}</h3>
                                    </div>
                                    <p class="p-text p-indent" style="margin-top: 0.125rem;">{{ $site->domaine }}</p>
                                    <p class="p-text--xs p-indent" style="margin-top: 0.25rem;">
                                        {{ $site->visites_count ?? 0 }} visites enregistrées
                                    </p>
                                </div>
                                <div class="p-actions p-indent">
                                    <form method="POST" action="{{ route('sites.toggle-actif', $site) }}">
                                        @csrf
                                        <x-button size="sm" type="submit" variant="{{ $site->actif ? 'default' : 'primary' }}">
                                            <x-custom-icon name="{{ $site->actif ? 'stop' : 'play' }}" class="w-3.5 h-3.5" />
                                            {{ $site->actif ? 'Désactiver' : 'Activer' }}
                                        </x-button>
                                    </form>
                                    <x-button size="sm" href="{{ route('sites.show', $site) }}">
                                        <x-custom-icon name="code" class="w-3.5 h-3.5" />
                                        Code
                                    </x-button>
                                    <form method="POST" action="{{ route('sites.destroy', $site) }}"
                                        onsubmit="return confirm('Supprimer ce site et toutes ses données ?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-button variant="danger" size="sm" type="submit">
                                            <x-custom-icon name="trash" class="w-3.5 h-3.5" />
                                            Supprimer
                                        </x-button>
                                    </form>
                                </div>
                            </div>
                        </x-card>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>