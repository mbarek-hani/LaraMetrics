<x-app-layout>
    <x-slot name="titre">
        Ajouter un site
    </x-slot>
    <div class="py-4">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-card>
                <form method="POST" action="{{ route('sites.store') }}">
                    @csrf

                    <div class="space-y-4">
                        <x-input name="nom" label="Nom du site" placeholder="Mon blog" :required="true"
                            value="{{ old('nom') }}" />

                        <x-input name="domaine" label="Domaine" placeholder="monsite.fr" :required="true"
                            aide="Sans http:// ni www. Exemple : monsite.fr" value="{{ old('domaine') }}" />
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-200 flex items-center gap-3">
                        <x-button variant="primary" type="submit">
                            Ajouter le site
                        </x-button>
                        <x-button href="{{ route('sites.index') }}">
                            Annuler
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>