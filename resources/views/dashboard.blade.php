<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Cartes de statistiques --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <x-stats-card
                    titre="Visiteurs aujourd'hui"
                    valeur="0"
                    icone="users"
                    couleur="indigo"
                />
                <x-stats-card
                    titre="Pages vues"
                    valeur="0"
                    icone="eye"
                    couleur="blue"
                />
                <x-stats-card
                    titre="Taux de rebond"
                    valeur="0%"
                    icone="arrow-trending-down"
                    couleur="green"
                />
            </div>

            {{-- Injection des widgets des plugins --}}
            @hook('dashboard.widgets')

        </div>
    </div>
</x-app-layout>
