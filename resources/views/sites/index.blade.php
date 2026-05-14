<x-app-layout>
    <x-slot name="titre">
        Sites
    </x-slot>
    <div class="p-page">
        <div class="p-container p-container--lg">
            <div class="p-page__header">
                <h2 class="p-page__title" style="margin-bottom: 0;">Sites</h2>
                <x-button variant="primary" href="{{ route('sites.create') }}" title="Ajouter un nouveau site">
                    <x-custom-icon name="plus" class="c-icon--sm" />
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
                <div class="p-grid p-grid--1 p-grid--2 p-grid--3 p-grid--4 p-grid--gap-md">
                    @foreach($sites as $site)
                        <x-card class="u-h-full">
                            <div class="u-flex-1">
                                <div class="p-row">
                                    <div
                                        class="p-status-dot {{ $site->actif ? 'p-status-dot--active' : 'p-status-dot--inactive' }}">
                                    </div>
                                    <h3 class="p-section__title">{{ $site->nom }}</h3>
                                </div>
                                <p class="p-text u-break-all" style="margin-top: 0.125rem;">{{ $site->domaine }}</p>
                                <p class="p-text--xs" style="margin-top: 0.5rem; color: var(--gray-400);">
                                    {{ $site->visites_count ?? 0 }} visites enregistrées
                                </p>
                            </div>
                            <x-slot name="footer">
                                <div class="p-actions u-w-full u-justify-between">
                                    <div class="p-row">
                                        <form method="POST" action="{{ route('sites.toggle-actif', $site) }}">
                                            @csrf
                                            <x-button size="sm" type="submit" variant="{{ $site->actif ? 'default' : 'primary' }}" title="{{ $site->actif ? 'Désactiver le site' : 'Activer le site' }}">
                                                <x-custom-icon name="{{ $site->actif ? 'stop' : 'play' }}" class="c-icon--xs" />
                                            </x-button>
                                        </form>
                                        <x-button href="{{ route('sites.show', $site) }}" size="sm" title="Voir les détails et le script">
                                            <x-custom-icon name="code" class="c-icon--xs" />
                                        </x-button>
                                    </div>
                                    <form method="POST" action="{{ route('sites.destroy', $site) }}"
                                        onsubmit="return confirm('Supprimer ce site ?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-button size="sm" variant="danger" type="submit" title="Supprimer définitivement ce site">
                                            <x-custom-icon name="trash" class="c-icon--xs" />
                                        </x-button>
                                    </form>
                                </div>
                            </x-slot>
                        </x-card>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>