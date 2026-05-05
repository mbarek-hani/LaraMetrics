<x-app-layout>
    <x-slot name="titre">
        Détails du Plugin : {{ $details['nom'] }}
    </x-slot>

    <div class="p-page">
        <div class="p-container p-container--md p-stack">
            <!-- En-tête -->
            <div class="p-page__header">
                <div class="p-row">
                    <h2 class="p-page__title" style="margin-bottom: 0;">{{ $details['nom'] }}</h2>
                </div>
                <div class="p-actions">
                    <x-button href="{{ route('plugins.index') }}" size="sm">
                        <x-custom-icon name="arrow-left" class="c-icon--sm" />
                        Retour
                    </x-button>
                    @if($details['actif'])
                        <form method="POST" action="{{ route('plugins.desactiver', $details['identifiant']) }}"
                            class="inline">
                            @csrf
                            <x-button type="submit" size="sm" variant="danger">
                                <x-custom-icon name="stop" class="c-icon--sm" />
                                Désactiver
                            </x-button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('plugins.activer', $details['identifiant']) }}" class="inline">
                            @csrf
                            <x-button type="submit" size="sm" variant="primary">
                                <x-custom-icon name="play" class="c-icon--sm" />
                                Activer
                            </x-button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Informations principales -->
            <div class="p-grid p-grid--3" style="align-items: start;">
                <!-- Colonne de gauche (2/3) -->
                <div class="p-dash__col-span-2 p-stack">
                    <x-card titre="Description">
                        <p class="p-text--muted" style="line-height: 1.625;">
                            {{ $details['description'] }}
                        </p>
                    </x-card>

                    <x-card titre="Fonctionnalités intégrées">
                        @if(empty($details['onglets']) && empty($details['hooks']) && empty($details['navigation']) && empty($details['reglages']))
                            <p class="p-text" style="font-style: italic;">Ce plugin n'expose aucune fonctionnalité
                                d'interface utilisateur standard.</p>
                        @else
                            @if(!empty($details['onglets']))
                                <div class="p-mb-3">
                                    <h4 class="p-section__title"
                                        style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        <x-custom-icon name="folder" class="p-section__icon" style="color: var(--gray-400);" />
                                        Onglets Dashboard
                                    </h4>
                                    <ul class="p-indent"
                                        style="list-style: disc inside; font-size: 0.875rem; color: var(--gray-600);">
                                        @foreach($details['onglets'] as $onglet)
                                            <li>{{ $onglet['label'] ?? 'Onglet' }} (ID: <code
                                                    class="p-code">{{ $onglet['id'] ?? 'N/A' }}</code>)</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(!empty($details['navigation']))
                                <div class="p-mb-3">
                                    <h4 class="p-section__title"
                                        style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        <x-custom-icon name="bars-3" class="p-section__icon" style="color: var(--gray-400);" />
                                        Liens de Navigation
                                    </h4>
                                    <ul class="p-indent"
                                        style="list-style: disc inside; font-size: 0.875rem; color: var(--gray-600);">
                                        @foreach($details['navigation'] as $nav)
                                            <li>{{ $nav['label'] ?? 'Lien' }} (Route: <code
                                                    class="p-code">{{ $nav['route'] ?? 'N/A' }}</code>)</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(!empty($details['hooks']))
                                <div class="p-mb-3">
                                    <h4 class="p-section__title"
                                        style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        <x-custom-icon name="bolt" class="p-section__icon" style="color: var(--gray-400);" />
                                        Hooks Visuels
                                    </h4>
                                    <div class="p-row p-row--wrap p-indent">
                                        @foreach($details['hooks'] as $hook)
                                            <span class="p-badge p-badge--info">
                                                {{ $hook }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($details['reglages']))
                                <div class="p-mb-3">
                                    <h4 class="p-section__title"
                                        style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                        <x-custom-icon name="cog" class="p-section__icon" style="color: var(--gray-400);" />
                                        Champs de Réglages
                                    </h4>
                                    <ul class="p-indent"
                                        style="list-style: disc inside; font-size: 0.875rem; color: var(--gray-600);">
                                        @foreach($details['reglages'] as $reglage)
                                            <li>{{ $reglage['label'] ?? 'Réglage' }} <span
                                                    style="color: var(--gray-400);">({{ $reglage['type'] ?? 'text' }})</span></li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endif
                </div>
                </x-card>
            </div>

            <!-- Colonne de droite (1/3) -->
            <div class="p-stack">
                <x-card titre="Métadonnées">
                    <div class="p-stack--sm" style="font-size: 0.875rem;">
                        <div class="p-row p-row--between"
                            style="padding: 0.25rem 0; border-bottom: 1px solid var(--gray-100);">
                            <span class="p-info-label">Statut</span>
                            @if($details['actif'])
                                <span class="p-badge p-badge--success">Actif</span>
                            @else
                                <span class="p-badge p-badge--neutral">Inactif</span>
                            @endif
                        </div>

                        <div class="p-row p-row--between"
                            style="padding: 0.25rem 0; border-bottom: 1px solid var(--gray-100);">
                            <span class="p-info-label">Version</span>
                            <span class="p-info-value">v{{ $details['version'] }}</span>
                        </div>

                        <div class="p-row p-row--between"
                            style="padding: 0.25rem 0; border-bottom: 1px solid var(--gray-100);">
                            <span class="p-info-label">Identifiant</span>
                            <code class="p-code" style="color: var(--gray-800);">{{ $details['identifiant'] }}</code>
                        </div>

                        <div class="p-row p-row--between"
                            style="padding: 0.25rem 0; border-bottom: 1px solid var(--gray-100);">
                            <span class="p-info-label">Auteur</span>
                            <span class="p-text--xs" style="color: var(--gray-900);">{{ $details['auteur'] }}</span>
                        </div>

                        <div class="p-row p-row--between"
                            style="padding: 0.25rem 0; border-bottom: 1px solid var(--gray-100);">
                            <span class="p-info-label">Licence</span>
                            <span class="p-text--xs" style="color: var(--gray-900);">{{ $details['licence'] }}</span>
                        </div>

                        @if($details['active_le'])
                            <div class="p-row p-row--between" style="padding: 0.25rem 0;">
                                <span class="p-info-label">Activé le</span>
                                <span class="p-info-value"
                                    style="font-weight: 400; font-size: 0.875rem;">{{ \Carbon\Carbon::parse($details['active_le'])->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </x-card>

                @if(!empty($details['url']))
                    <x-card>
                        <div class="p-row" style="align-items: flex-start; gap: 0.75rem;">
                            <x-custom-icon name="globe-alt" class="p-section__icon"
                                style="color: var(--gray-400); width: 1.25rem; height: 1.25rem;" />
                            <div>
                                <h4 class="p-section__title">Site Web</h4>
                                <a href="{{ $details['url'] }}" target="_blank" rel="noopener noreferrer" class="p-text"
                                    style="color: var(--info-text); text-decoration: none; display: block; margin-top: 0.25rem; word-break: break-all;">
                                    {{ $details['url'] }}
                                </a>
                            </div>
                        </div>
                    </x-card>
                @endif
            </div>
        </div>

    </div>
    </div>
</x-app-layout>