<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Sites</h2>
            <x-button variant="primary" href="{{ route('sites.create') }}">
                <x-icon name="plus" class="w-4 h-4" />
                Ajouter un site
            </x-button>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('succes'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded p-3">
                    {{ session('succes') }}
                </div>
            @endif

            @if($sites->isEmpty())
                <x-card>
                    <div class="text-center py-8">
                        <x-icon name="globe" class="w-10 h-10 text-gray-300 mx-auto" />
                        <p class="mt-2 text-sm text-gray-500">Aucun site pour le moment.</p>
                        <div class="mt-3">
                            <x-button variant="primary" href="{{ route('sites.create') }}">
                                <x-icon name="plus" class="w-4 h-4" />
                                Ajouter un site
                            </x-button>
                        </div>
                    </div>
                </x-card>
            @else
                <div class="space-y-3">
                    @foreach($sites as $site)
                        <x-card>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full {{ $site->actif ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                                        <h3 class="text-sm font-semibold text-gray-900">{{ $site->nom }}</h3>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-0.5 ml-4">{{ $site->domaine }}</p>
                                    <p class="text-xs text-gray-400 mt-1 ml-4">
                                        {{ $site->visites_count ?? 0 }} visites enregistrées
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 ml-4 sm:ml-0">
                                    <x-button size="sm" href="{{ route('sites.show', $site) }}">
                                        <x-icon name="code" class="w-3.5 h-3.5" />
                                        Code
                                    </x-button>
                                    <form method="POST" action="{{ route('sites.destroy', $site) }}"
                                          onsubmit="return confirm('Supprimer ce site et toutes ses données ?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-button variant="danger" size="sm" type="submit">
                                            <x-icon name="trash" class="w-3.5 h-3.5" />
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
