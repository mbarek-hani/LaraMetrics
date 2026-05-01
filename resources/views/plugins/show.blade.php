<x-app-layout>
    <x-slot name="titre">
        Détails du Plugin : {{ $details['nom'] }}
    </x-slot>

    <div class="py-4">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- En-tête -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-bold text-gray-900">{{ $details['nom'] }}</h2>
                </div>
                <div class="flex gap-2">
                    <x-button href="{{ route('plugins.index') }}" size="sm" variant="secondary">
                        <x-custom-icon name="arrow-left" class="w-4 h-4" />
                        Retour
                    </x-button>
                    @if($details['actif'])
                        <form method="POST" action="{{ route('plugins.desactiver', $details['identifiant']) }}" class="inline">
                            @csrf
                            <x-button type="submit" size="sm" variant="danger">
                                <x-custom-icon name="stop" class="w-4 h-4" />
                                Désactiver
                            </x-button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('plugins.activer', $details['identifiant']) }}" class="inline">
                            @csrf
                            <x-button type="submit" size="sm" variant="primary">
                                <x-custom-icon name="play" class="w-4 h-4" />
                                Activer
                            </x-button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Informations principales -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Colonne de gauche (2/3) -->
                <div class="md:col-span-2 space-y-6">
                    <x-card titre="Description">
                        <p class="text-gray-700 text-sm leading-relaxed">
                            {{ $details['description'] }}
                        </p>
                    </x-card>

                    <x-card titre="Fonctionnalités intégrées">
                        <div class="space-y-4">
                            @if(empty($details['onglets']) && empty($details['hooks']) && empty($details['navigation']) && empty($details['reglages']))
                                <p class="text-sm text-gray-500 italic">Ce plugin n'expose aucune fonctionnalité d'interface utilisateur standard.</p>
                            @else
                                @if(!empty($details['onglets']))
                                    <div>
                                        <h4 class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-2 flex items-center gap-2">
                                            <x-custom-icon name="folder" class="w-4 h-4 text-gray-400" /> Onglets Dashboard
                                        </h4>
                                        <ul class="list-disc list-inside text-sm text-gray-600 ml-4">
                                            @foreach($details['onglets'] as $onglet)
                                                <li>{{ $onglet['label'] ?? 'Onglet' }} (ID: <code class="text-xs bg-gray-100 px-1 rounded">{{ $onglet['id'] ?? 'N/A' }}</code>)</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                @if(!empty($details['navigation']))
                                    <div>
                                        <h4 class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-2 mt-4 flex items-center gap-2">
                                            <x-custom-icon name="bars-3" class="w-4 h-4 text-gray-400" /> Liens de Navigation
                                        </h4>
                                        <ul class="list-disc list-inside text-sm text-gray-600 ml-4">
                                            @foreach($details['navigation'] as $nav)
                                                <li>{{ $nav['label'] ?? 'Lien' }} (Route: <code class="text-xs bg-gray-100 px-1 rounded">{{ $nav['route'] ?? 'N/A' }}</code>)</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if(!empty($details['hooks']))
                                    <div>
                                        <h4 class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-2 mt-4 flex items-center gap-2">
                                            <x-custom-icon name="bolt" class="w-4 h-4 text-gray-400" /> Hooks Visuels
                                        </h4>
                                        <div class="flex flex-wrap gap-2 ml-4">
                                            @foreach($details['hooks'] as $hook)
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 rounded">
                                                    {{ $hook }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                @if(!empty($details['reglages']))
                                    <div>
                                        <h4 class="text-xs font-semibold text-gray-900 uppercase tracking-wider mb-2 mt-4 flex items-center gap-2">
                                            <x-custom-icon name="cog" class="w-4 h-4 text-gray-400" /> Champs de Réglages
                                        </h4>
                                        <ul class="list-disc list-inside text-sm text-gray-600 ml-4">
                                            @foreach($details['reglages'] as $reglage)
                                                <li>{{ $reglage['label'] ?? 'Réglage' }} <span class="text-gray-400">({{ $reglage['type'] ?? 'text' }})</span></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </x-card>
                </div>

                <!-- Colonne de droite (1/3) -->
                <div class="space-y-6">
                    <x-card titre="Métadonnées">
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center py-1 border-b border-gray-100">
                                <span class="text-gray-500">Statut</span>
                                @if($details['actif'])
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-green-50 text-green-700 border border-green-200 rounded">Actif</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200 rounded">Inactif</span>
                                @endif
                            </div>
                            
                            <div class="flex justify-between items-center py-1 border-b border-gray-100">
                                <span class="text-gray-500">Version</span>
                                <span class="font-medium text-gray-900">v{{ $details['version'] }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center py-1 border-b border-gray-100">
                                <span class="text-gray-500">Identifiant</span>
                                <code class="text-xs bg-gray-100 px-1 py-0.5 rounded text-gray-800">{{ $details['identifiant'] }}</code>
                            </div>

                            <div class="flex justify-between items-center py-1 border-b border-gray-100">
                                <span class="text-gray-500">Auteur</span>
                                <span class="text-xs text-gray-900">{{ $details['auteur'] }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center py-1 border-b border-gray-100">
                                <span class="text-gray-500">Licence</span>
                                <span class="text-xs text-gray-900">{{ $details['licence'] }}</span>
                            </div>
                            
                            @if($details['active_le'])
                                <div class="flex justify-between items-center py-1">
                                    <span class="text-gray-500">Activé le</span>
                                    <span class="text-gray-900">{{ \Carbon\Carbon::parse($details['active_le'])->format('d/m/Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </x-card>

                    @if(!empty($details['url']))
                        <x-card>
                            <div class="flex items-start gap-3">
                                <x-custom-icon name="globe-alt" class="w-5 h-5 text-gray-400" />
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900">Site Web</h4>
                                    <a href="{{ $details['url'] }}" target="_blank" rel="noopener noreferrer" class="text-sm text-blue-600 hover:text-blue-800 hover:underline mt-1 block break-all">
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
