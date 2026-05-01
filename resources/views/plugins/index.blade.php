<x-app-layout>
    <x-slot name="titre">
        Plugins
    </x-slot>
    <div class="py-4">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Plugins</h2>
            @if(empty($plugins))
                <x-card>
                    <div class="text-center py-8">
                        <x-custom-icon name="puzzle" class="w-10 h-10 text-gray-300 mx-auto" />
                        <h3 class="mt-2 text-sm font-semibold text-gray-900">Aucun plugin détecté</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Placez vos plugins dans le dossier <code class="text-xs bg-gray-100 px-1 py-0.5 rounded">/plugins</code>
                        </p>
                    </div>
                </x-card>
            @else
                <div class="space-y-3">
                    @foreach($plugins as $plugin)
                        <x-card>
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5">
                                        <x-custom-icon name="puzzle" class="w-5 h-5 text-gray-400" />
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-sm font-semibold text-gray-900">
                                                {{ $plugin['nom'] }}
                                            </h3>
                                            <span class="text-xs text-gray-400">v{{ $plugin['version'] }}</span>
                                            @if($plugin['actif'])
                                                <span class="inline-flex items-center px-1.5 py-0.5 text-xs font-medium bg-green-50 text-green-700 border border-green-200 rounded">
                                                    Actif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-1.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-500 border border-gray-200 rounded">
                                                    Inactif
                                                </span>
                                            @endif
                                        </div>

                                        @if($plugin['description'])
                                            <p class="text-sm text-gray-500 mt-1">{{ $plugin['description'] }}</p>
                                        @endif

                                        <p class="text-xs text-gray-400 mt-1">
                                            Par {{ $plugin['auteur'] }}
                                        </p>

                                        @if(!empty($plugin['hooks']))
                                            <div class="flex items-center gap-1 mt-2">
                                                <x-custom-icon name="bolt" class="w-3 h-3 text-gray-400" />
                                                <span class="text-xs text-gray-400">
                                                    Hooks : {{ implode(', ', $plugin['hooks']) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 sm:shrink-0 ml-8 sm:ml-0">
                                    <x-button href="{{ route('plugins.show', $plugin['identifiant']) }}" size="sm">
                                        <x-custom-icon name="eye" class="w-3.5 h-3.5" />
                                        Détails
                                    </x-button>
                                    @if($plugin['actif'])
                                        <form method="POST" action="{{ route('plugins.desactiver', $plugin['identifiant']) }}">
                                            @csrf
                                            <x-button type="submit" variant="danger" size="sm">
                                                <x-custom-icon name="stop" class="w-3.5 h-3.5" />
                                                Désactiver
                                            </x-button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('plugins.activer', $plugin['identifiant']) }}">
                                            @csrf
                                            <x-button variant="primary" type="submit" size="sm">
                                                <x-custom-icon name="play" class="w-3.5 h-3.5" />
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
