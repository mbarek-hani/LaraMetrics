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
                    titre="Visiteurs cette semaine"
                    :valeur="$resume['visiteurs_uniques']"
                    icone="users"
                    couleur="indigo"
                />
                <x-stats-card
                    titre="Pages vues"
                    :valeur="$resume['pages_vues']"
                    icone="eye"
                    couleur="blue"
                />
                <x-stats-card
                    titre="Taux de rebond"
                    :valeur="$resume['taux_rebond'] . '%'"
                    icone="arrow-trending-down"
                    couleur="green"
                />
                <x-stats-card
                    titre="Nouvelles session"
                    :valeur="$resume['nouvelles_sessions']"
                    icone="arrow-trending-down"
                    couleur="green"
                />
                <x-stats-card
                    titre="Durées Moyenne"
                    :valeur="$resume['duree_moyenne']"
                    icone="arrow-trending-down"
                    couleur="green"
                />
            </div>

            {{-- Injection des widgets des plugins --}}
            @hook('dashboard.widgets')

        </div>
    </div>
</x-app-layout>
