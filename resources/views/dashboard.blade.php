<x-app-layout titre="Tableau de bord">
    <x-slot name="titre">
        Tableau de bord
    </x-slot>

    <div class="py-4">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Tableau de bord</h2>

            @if($aucunSite)
                <x-card>
                    <div class="text-center py-8">
                        <x-custom-icon name="globe" class="w-10 h-10 text-gray-300 mx-auto" />
                        <h3 class="mt-2 text-sm font-semibold text-gray-900">Aucun site configuré</h3>
                        <p class="mt-1 text-sm text-gray-500">Ajoutez votre premier site pour commencer.</p>
                        <div class="mt-3">
                            <x-button variant="primary" href="{{ route('sites.create') }}">
                                <x-custom-icon name="plus" class="w-4 h-4" />
                                Ajouter un site
                            </x-button>
                        </div>
                    </div>
                </x-card>
            @else

            {{-- Alpine : état global du dashboard partagé avec les plugins --}}
            <div
                x-data="dashboard()"
                x-init="chargerStats()"
                id="dashboard-root"
            >
                {{-- Contrôles --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <select
                        x-model="siteId"
                        @change="chargerStats()"
                        class="rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}"
                                {{ $site->id === $siteCourant->id ? 'selected' : '' }}>
                                {{ $site->nom }} — {{ $site->domaine }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Période Desktop --}}
                    <div class="hidden sm:flex items-center border border-gray-300 rounded divide-x divide-gray-300">
                        <template x-for="p in periodes" :key="p.valeur">
                            <button
                                @click="periode = p.valeur; chargerStats()"
                                :class="periode === p.valeur
                                    ? 'bg-gray-100 text-gray-900 font-semibold'
                                    : 'bg-white text-gray-600 hover:bg-gray-50'"
                                class="px-3 py-1.5 text-xs transition first:rounded-l last:rounded-r"
                                x-text="p.label"
                            ></button>
                        </template>
                    </div>

                    {{-- Période Mobile --}}
                    <select
                        x-model="periode"
                        @change="chargerStats()"
                        class="sm:hidden rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <template x-for="p in periodes" :key="p.valeur">
                            <option :value="p.valeur" x-text="p.label"></option>
                        </template>
                    </select>
                </div>

                {{-- Onglets --}}
                <div class="border-b border-gray-200 mb-4 overflow-x-auto">
                    <nav class="flex gap-0 min-w-max">

                        <button
                            @click="ongletActif = 'apercu'"
                            :class="ongletActif === 'apercu'
                                ? 'border-b-2 border-gray-900 text-gray-900'
                                : 'text-gray-500 hover:text-gray-700'"
                            class="flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium transition"
                        >
                            <x-custom-icon name="chart-bar" class="w-4 h-4" />
                            Vue d'ensemble
                        </button>

                        <button
                            @click="ongletActif = 'evenements'"
                            :class="ongletActif === 'evenements'
                                ? 'border-b-2 border-gray-900 text-gray-900'
                                : 'text-gray-500 hover:text-gray-700'"
                            class="flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium transition"
                        >
                            <x-custom-icon name="cursor-click" class="w-4 h-4" />
                            Événements
                        </button>

                        {{-- Onglets plugins --}}
                        @foreach($onglets as $onglet)
                            <button
                                @click="ongletActif = '{{ $onglet['id'] }}'"
                                :class="ongletActif === '{{ $onglet['id'] }}'
                                    ? 'border-b-2 border-gray-900 text-gray-900'
                                    : 'text-gray-500 hover:text-gray-700'"
                                class="flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium transition"
                            >
                                <x-custom-icon :name="$onglet['icone']" class="w-4 h-4" />
                                {{ $onglet['label'] }}
                            </button>
                        @endforeach
                    </nav>
                </div>

                {{-- Chargement --}}
                <div x-show="chargement" x-cloak class="flex items-center justify-center py-16">
                    <x-custom-icon name="arrow-path" class="w-5 h-5 text-gray-400 animate-spin" />
                    <span class="ml-2 text-sm text-gray-500">Chargement...</span>
                </div>

                {{-- Erreur --}}
                <div x-show="erreur" x-cloak class="bg-red-50 border border-red-200 rounded p-3 mb-4">
                    <p class="text-sm text-red-700" x-text="erreur"></p>
                </div>

                <div x-show="!chargement && !erreur" x-cloak>

                    {{-- ══════ VUE D'ENSEMBLE ══════ --}}
                    <div x-show="ongletActif === 'apercu'" x-cloak>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                            <x-stats-card titre="Visiteurs uniques" icon="users">
                                <x-slot:valeur>
                                    <span x-text="stats?.resume?.visiteurs_uniques ?? 0"></span>
                                </x-slot:valeur>
                            </x-stats-card>
                            <x-stats-card titre="Pages vues" icon="eye">
                                <x-slot:valeur>
                                    <span x-text="stats?.resume?.pages_vues ?? 0"></span>
                                </x-slot:valeur>
                            </x-stats-card>
                            <x-stats-card titre="Taux de rebond" icon="arrow-trending-down">
                                <x-slot:valeur>
                                    <span x-text="(stats?.resume?.taux_rebond ?? 0) + '%'"></span>
                                </x-slot:valeur>
                            </x-stats-card>
                            <x-stats-card titre="Durée moyenne" icon="clock">
                                <x-slot:valeur>
                                    <span x-text="formaterDuree(stats?.resume?.duree_moyenne ?? 0)"></span>
                                </x-slot:valeur>
                            </x-stats-card>
                        </div>

                        <x-card titre="Évolution du trafic" class="mb-4">
                            <div class="h-56">
                                <canvas x-ref="graphique"></canvas>
                            </div>
                        </x-card>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                            <x-card titre="Pages populaires" :padding="false">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200 text-left">
                                            <th class="px-4 py-2 text-xs font-medium text-gray-500">Page</th>
                                            <th class="px-4 py-2 text-xs font-medium text-gray-500 text-right">Vues</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="page in (stats?.top_pages ?? []).slice(0, 8)" :key="page.chemin">
                                            <tr class="border-b border-gray-100">
                                                <td class="px-4 py-2 text-gray-900 truncate max-w-[200px]" x-text="page.chemin"></td>
                                                <td class="px-4 py-2 text-gray-600 text-right" x-text="page.vues"></td>
                                            </tr>
                                        </template>
                                        <template x-if="!stats?.top_pages?.length">
                                            <tr><td colspan="2" class="px-4 py-6 text-center text-gray-400 text-sm">Aucune donnée</td></tr>
                                        </template>
                                    </tbody>
                                </table>
                            </x-card>

                            <x-card titre="Sources de trafic" :padding="false">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200 text-left">
                                            <th class="px-4 py-2 text-xs font-medium text-gray-500">Source</th>
                                            <th class="px-4 py-2 text-xs font-medium text-gray-500 text-right">Visiteurs</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="ref in (stats?.top_referents ?? []).slice(0, 8)" :key="ref.referent_domaine">
                                            <tr class="border-b border-gray-100">
                                                <td class="px-4 py-2">
                                                    <div class="flex items-center gap-2">
                                                        <img :src="'https://www.google.com/s2/favicons?domain=' + ref.referent_domaine + '&sz=16'" class="w-4 h-4">
                                                        <span class="text-gray-900 truncate" x-text="ref.referent_domaine"></span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-2 text-gray-600 text-right" x-text="ref.visiteurs"></td>
                                            </tr>
                                        </template>
                                        <template x-if="!stats?.top_referents?.length">
                                            <tr><td colspan="2" class="px-4 py-6 text-center text-gray-400 text-sm">Aucun référent</td></tr>
                                        </template>
                                    </tbody>
                                </table>
                            </x-card>

                            <x-card titre="Pays">
                                <div class="space-y-2">
                                    <template x-for="pays in (stats?.top_pays ?? []).slice(0, 8)" :key="pays.pays_code">
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center gap-2">
                                                <span x-text="drapeau(pays.pays_code)"></span>
                                                <span class="text-gray-900" x-text="pays.pays_nom || pays.pays_code"></span>
                                            </div>
                                            <span class="text-gray-600" x-text="pays.visiteurs"></span>
                                        </div>
                                    </template>
                                    <template x-if="!stats?.top_pays?.length">
                                        <p class="text-center text-gray-400 text-sm py-4">Aucune donnée</p>
                                    </template>
                                </div>
                            </x-card>

                            <x-card titre="Appareils">
                                <div class="flex items-center gap-6">
                                    <div class="w-28 h-28 shrink-0">
                                        <canvas x-ref="graphiqueAppareils"></canvas>
                                    </div>
                                    <div class="space-y-2 flex-1">
                                        <template x-for="app in stats?.appareils ?? []" :key="app.appareil">
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-gray-900 capitalize" x-text="app.appareil"></span>
                                                <span class="text-gray-600" x-text="app.visiteurs"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </x-card>
                        </div>

                        @hook('dashboard.widgets')
                    </div>

                    {{-- ══════ ÉVÉNEMENTS ══════ --}}
                    <div x-show="ongletActif === 'evenements'" x-cloak>
                        {{-- Cartes de statistiques --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                            <x-stats-card titre="Total événements" icon="cursor-click">
                                <x-slot:valeur>
                                    <span x-text="totalEvenements()"></span>
                                </x-slot:valeur>
                            </x-stats-card>
                            <x-stats-card titre="Événements / Session" icon="arrows-right-left">
                                <x-slot:valeur>
                                    <span x-text="moyenneEvtParSession()"></span>
                                </x-slot:valeur>
                            </x-stats-card>
                            <x-stats-card titre="Types différents" icon="tag">
                                <x-slot:valeur>
                                    <span x-text="stats?.evenements_par_nom?.length ?? 0"></span>
                                </x-slot:valeur>
                            </x-stats-card>
                            <x-stats-card titre="Sessions actives" icon="users">
                                <x-slot:valeur>
                                    <span x-text="totalSessionsEvenements()"></span>
                                </x-slot:valeur>
                            </x-stats-card>
                        </div>

                        {{-- Graphique d'activité Temporelle --}}
                        <x-card titre="Activité des événements" class="mb-4">
                            <div class="h-64">
                                <canvas x-ref="graphiqueEvenements"></canvas>
                            </div>
                        </x-card>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            {{-- Répartition par nom + Graphique Doughnut --}}
                            <x-card titre="Répartition des types">
                                <div class="flex flex-col md:flex-row items-center gap-4">
                                    <div class="w-40 h-40 shrink-0">
                                        <canvas x-ref="graphiqueEvtRepart"></canvas>
                                    </div>
                                    <div class="flex-1 w-full">
                                        <table class="w-full text-sm">
                                            <tbody>
                                                <template x-for="(evt, index) in (stats?.evenements_par_nom ?? []).slice(0, 5)" :key="evt.nom">
                                                    <tr class="border-b border-gray-50 last:border-0">
                                                        <td class="py-2 flex items-center gap-2">
                                                            <div class="w-2 h-2 rounded-full" :style="'background-color: ' + couleursChart[index]"></div>
                                                            <span class="text-gray-900 font-medium" x-text="evt.nom"></span>
                                                        </td>
                                                        <td class="py-2 text-right text-gray-600" x-text="evt.total"></td>
                                                        <td class="py-2 text-right text-gray-400 text-xs" x-text="Math.round((evt.total / totalEvenements()) * 100) + '%'"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </x-card>

                            {{-- Top des pages où se produisent les événements --}}
                            <x-card titre="Pages les plus interactives" :padding="false">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200 text-left bg-gray-50/50">
                                            <th class="px-4 py-2 text-xs font-medium text-gray-500">URL</th>
                                            <th class="px-4 py-2 text-xs font-medium text-gray-500 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="page in (stats?.evenements_par_page ?? []).slice(0, 6)" :key="page.chemin">
                                            <tr class="border-b border-gray-100 last:border-0">
                                                <td class="px-4 py-2 text-gray-900 truncate max-w-[200px]" x-text="page.chemin"></td>
                                                <td class="px-4 py-2 text-gray-600 text-right font-mono" x-text="page.total"></td>
                                            </tr>
                                        </template>
                                        <template x-if="!stats?.evenements_par_page?.length">
                                            <tr><td colspan="2" class="px-4 py-8 text-center text-gray-400">Aucune donnée contextuelle</td></tr>
                                        </template>
                                    </tbody>
                                </table>
                            </x-card>

                            {{-- Journal des derniers événements --}}
                            <x-card titre="Dernière activité" :padding="false" class="lg:col-span-2">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200 text-left bg-gray-50/50">
                                            <th class="px-4 py-2 text-xs font-medium text-gray-500">Événement</th>
                                            <th class="px-4 py-2 text-xs font-medium text-gray-500">Page</th>
                                            <th class="px-4 py-2 text-xs font-medium text-gray-500 text-right">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="evt in (stats?.derniers_evenements ?? []).slice(0, 8)" :key="evt.id">
                                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                                <td class="px-4 py-2">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800" x-text="evt.nom"></span>
                                                </td>
                                                <td class="px-4 py-2 text-gray-500 truncate max-w-[250px]" x-text="evt.chemin"></td>
                                                <td class="px-4 py-2 text-gray-400 text-right text-xs" x-text="formaterDate(evt.cree_le)"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </x-card>
                        </div>
                    </div>

                    {{-- ══════ ONGLETS PLUGINS ══════ --}}
                    @foreach($onglets as $onglet)
                        <div x-show="ongletActif === '{{ $onglet['id'] }}'" x-cloak>
                            @hook('tab.' . $onglet['id'])
                        </div>
                    @endforeach

                </div>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    function dashboard() {
        return {
            siteId      : {{ $siteCourant->id ?? 0 }},
            periode     : '7j',
            ongletActif : 'apercu',
            chargement  : true,
            erreur      : null,
            stats       : null,
            _chart      : null,
            _chartApp   : null,
            _chartEvt   : null,
            _chartEvtRepart: null,
            couleursChart: ['#6366f1', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981'], // Indigo, Violet, Pink, Amber, Emerald

            periodes: [
                { valeur: 'aujourdhui',  label: "Aujourd'hui" },
                { valeur: '7j',          label: '7 jours' },
                { valeur: '30j',         label: '30 jours' },
                { valeur: 'ce_mois',     label: 'Ce mois' },
                { valeur: 'cette_annee', label: 'Année' },
            ],

            async chargerStats() {
                this.chargement = true;
                this.erreur     = null;
                try {
                    const p = new URLSearchParams({ site_id: this.siteId, periode: this.periode });
                    const r = await fetch('/dashboard/stats?' + p, {
                        headers: {
                            'Accept'       : 'application/json',
                            'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });
                    if (!r.ok) throw new Error('Erreur ' + r.status);
                    this.stats = await r.json();
                    this.$nextTick(() => {
                        this.dessinerGraphique();
                        this.dessinerAppareils();
                        this.dessinerEvenements();
                        this.dessinerRepartitionEvenements();
                    });
                } catch (e) {
                    this.erreur = e.message;
                } finally {
                    this.chargement = false;
                }
            },

            dessinerGraphique() {
                const c = this.$refs.graphique;
                if (!c) return;
                if (this._chart) this._chart.destroy();
                const d = this.stats?.evolution ?? [];
                this._chart = new Chart(c, {
                    type: 'line',
                    data: {
                        labels: d.map(e => new Date(e.date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })),
                        datasets: [
                            {
                                label: 'Visiteurs',
                                data: d.map(e => e.visiteurs),
                                borderColor: '#f71',
                                backgroundColor: 'rgba(55,65,81,0.05)',
                                fill: true, tension: 0.3, borderWidth: 2, pointRadius: 2,
                            },
                            {
                                label: 'Pages vues',
                                data: d.map(e => e.pages_vues),
                                borderColor: '#93f',
                                backgroundColor: 'rgba(156,163,175,0.05)',
                                fill: true, tension: 0.3, borderWidth: 2, pointRadius: 2,
                            },
                        ],
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                        plugins: { legend: { position: 'top', labels: { usePointStyle: true, padding: 15, font: { size: 11 } } } },
                    },
                });
            },

            dessinerAppareils() {
                const c = this.$refs.graphiqueAppareils;
                if (!c) return;
                if (this._chartApp) this._chartApp.destroy();
                const d = this.stats?.appareils ?? [];
                if (!d.length) return;
                this._chartApp = new Chart(c, {
                    type: 'doughnut',
                    data: {
                        labels: d.map(a => a.appareil),
                        datasets: [{ data: d.map(a => a.visiteurs), backgroundColor: ['#fab', '#afb', '#abf', '#666'], borderWidth: 0 }],
                    },
                    options: { responsive: true, maintainAspectRatio: true, cutout: '60%', plugins: { legend: { display: false } } },
                });
            },

            formaterDuree(s) {
                if (!s || s < 1) return '0s';
                const m = Math.floor(s / 60), sec = Math.floor(s % 60);
                return m > 0 ? m + 'm ' + sec + 's' : sec + 's';
            },

            formaterDate(d) {
                if (!d) return '';
                return new Date(d).toLocaleDateString('fr-FR', {
                    day: 'numeric', month: 'short',
                    hour: '2-digit', minute: '2-digit',
                });
            },

            drapeau(code) {
                if (!code || code.length !== 2) return '';
                return String.fromCodePoint(
                    ...code.toUpperCase().split('').map(c => c.charCodeAt(0) + 127397)
                );
            },
            dessinerEvenements() {
                const c = this.$refs.graphiqueEvenements;
                if (!c) return;
                if (this._chartEvt) this._chartEvt.destroy();
                const d = this.stats?.evenements_par_jour ?? [];

                this._chartEvt = new Chart(c, {
                    type: 'line', // Changé de 'bar' à 'line' pour mieux voir la tendance
                    data: {
                        labels: d.map(e => new Date(e.date).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })),
                        datasets: [{
                            label: 'Événements',
                            data: d.map(e => e.total),
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 3
                        }],
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { display: false } },
                            x: { grid: { display: false } }
                        }
                    },
                });
            },

            // Nouveau graphique de répartition
            dessinerRepartitionEvenements() {
                const c = this.$refs.graphiqueEvtRepart;
                if (!c) return;
                if (this._chartEvtRepart) this._chartEvtRepart.destroy();

                const data = this.stats?.evenements_par_nom ?? [];
                if (!data.length) return;

                this._chartEvtRepart = new Chart(c, {
                    type: 'doughnut',
                    data: {
                        labels: data.map(e => e.nom),
                        datasets: [{
                            data: data.map(e => e.total),
                            backgroundColor: this.couleursChart,
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '75%',
                        plugins: { legend: { display: false } }
                    }
                });
            },

            totalEvenements() {
                return (this.stats?.evenements_par_nom ?? []).reduce((s, e) => s + e.total, 0);
            },

            totalSessionsEvenements() {
                return (this.stats?.evenements_par_nom ?? []).reduce((s, e) => s + (e.sessions || 0), 0);
            },

            // Nouvelle métrique
            moyenneEvtParSession() {
                const total = this.totalEvenements();
                const sessions = this.totalSessionsEvenements();
                if (!sessions) return 0;
                return (total / sessions).toFixed(1);
            },
        }
    }
    </script>
    @endpush
</x-app-layout>
