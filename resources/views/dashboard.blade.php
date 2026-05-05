<x-app-layout titre="Tableau de bord">
    <x-slot name="titre">
        Tableau de bord
    </x-slot>

    <div class="p-page">
        <div class="p-container p-container--lg">
            <h2 class="p-page__title">Tableau de bord</h2>

            @if($aucunSite)
                <x-card>
                    <div class="p-empty">
                        <x-custom-icon name="globe" class="p-empty__icon" />
                        <h3 class="p-empty__title">Aucun site configuré</h3>
                        <p class="p-empty__text">Ajoutez votre premier site pour commencer.</p>
                        <div class="p-mt-3">
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
                <div class="p-dash__controls">
                    <select
                        x-model="siteId"
                        @change="chargerStats()"
                        class="p-dash__select"
                    >
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}"
                                {{ $site->id === $siteCourant->id ? 'selected' : '' }}>
                                {{ $site->nom }} — {{ $site->domaine }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Période Desktop --}}
                    <div class="p-dash__periods">
                        <template x-for="p in periodes" :key="p.valeur">
                            <button
                                @click="periode = p.valeur; chargerStats()"
                                :class="periode === p.valeur
                                    ? 'p-dash__period-btn--active'
                                    : ''"
                                class="p-dash__period-btn"
                                x-text="p.label"
                            ></button>
                        </template>
                    </div>

                    {{-- Période Mobile --}}
                    <select
                        x-model="periode"
                        @change="chargerStats()"
                        class="p-dash__period-mobile"
                    >
                        <template x-for="p in periodes" :key="p.valeur">
                            <option :value="p.valeur" x-text="p.label"></option>
                        </template>
                    </select>
                </div>

                {{-- Onglets --}}
                <div class="p-dash__tabs">
                    <nav class="p-dash__tab-nav">

                        <button
                            @click="ongletActif = 'apercu'"
                            :class="ongletActif === 'apercu'
                                ? 'p-dash__tab--active'
                                : ''"
                            class="p-dash__tab"
                        >
                            <x-custom-icon name="chart-bar" class="w-4 h-4" />
                            Vue d'ensemble
                        </button>

                        <button
                            @click="ongletActif = 'evenements'"
                            :class="ongletActif === 'evenements'
                                ? 'p-dash__tab--active'
                                : ''"
                            class="p-dash__tab"
                        >
                            <x-custom-icon name="cursor-click" class="w-4 h-4" />
                            Événements
                        </button>

                        {{-- Onglets plugins --}}
                        @foreach($onglets as $onglet)
                            <button
                                @click="ongletActif = '{{ $onglet['id'] }}'"
                                :class="ongletActif === '{{ $onglet['id'] }}'
                                    ? 'p-dash__tab--active'
                                    : ''"
                                class="p-dash__tab"
                            >
                                <x-custom-icon :name="$onglet['icone']" class="w-4 h-4" />
                                {{ $onglet['label'] }}
                            </button>
                        @endforeach
                    </nav>
                </div>

                {{-- Chargement --}}
                <div x-show="chargement" x-cloak class="p-dash__loading">
                    <x-custom-icon name="arrow-path" class="p-dash__loading-icon" />
                    <span class="p-dash__loading-text">Chargement...</span>
                </div>

                {{-- Erreur --}}
                <div x-show="erreur" x-cloak class="p-dash__error">
                    <p class="p-dash__error-text" x-text="erreur"></p>
                </div>

                <div x-show="!chargement && !erreur" x-cloak>

                    {{-- ══════ VUE D'ENSEMBLE ══════ --}}
                    <div x-show="ongletActif === 'apercu'" x-cloak>
                        <div class="p-grid p-grid--2 p-grid--4 p-mb-3">
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

                        <x-card titre="Évolution du trafic" class="p-mb-3">
                            <div class="p-dash__chart">
                                <canvas x-ref="graphique"></canvas>
                            </div>
                        </x-card>

                        <div class="p-dash__grid-2">

                            <x-card titre="Pages populaires" :padding="false">
                                <table class="p-dash__table">
                                    <thead>
                                        <tr>
                                            <th>Page</th>
                                            <th class="p-dash__table-th--right">Vues</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="page in (stats?.top_pages ?? []).slice(0, 8)" :key="page.chemin">
                                            <tr>
                                                <td class="p-dash__table-cell--truncate" x-text="page.chemin"></td>
                                                <td class="p-dash__table-cell--right" x-text="page.vues"></td>
                                            </tr>
                                        </template>
                                        <template x-if="!stats?.top_pages?.length">
                                            <tr><td colspan="2" class="p-dash__table-cell--center-lg">Aucune donnée</td></tr>
                                        </template>
                                    </tbody>
                                </table>
                            </x-card>

                            <x-card titre="Sources de trafic" :padding="false">
                                <table class="p-dash__table">
                                    <thead>
                                        <tr>
                                            <th>Source</th>
                                            <th class="p-dash__table-th--right">Visiteurs</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="ref in (stats?.top_referents ?? []).slice(0, 8)" :key="ref.referent_domaine">
                                            <tr>
                                                <td>
                                                    <div class="p-dash__list-name">
                                                        <img :src="'https://www.google.com/s2/favicons?domain=' + ref.referent_domaine + '&sz=16'" class="p-dash__favicon">
                                                        <span class="p-dash__table-cell--truncate" x-text="ref.referent_domaine"></span>
                                                    </div>
                                                </td>
                                                <td class="p-dash__table-cell--right" x-text="ref.visiteurs"></td>
                                            </tr>
                                        </template>
                                        <template x-if="!stats?.top_referents?.length">
                                            <tr><td colspan="2" class="p-dash__table-cell--center-lg">Aucun référent</td></tr>
                                        </template>
                                    </tbody>
                                </table>
                            </x-card>

                            <x-card titre="Pays">
                                <div class="p-dash__list-stack">
                                    <template x-for="pays in (stats?.top_pays ?? []).slice(0, 8)" :key="pays.pays_code">
                                        <div class="p-dash__list-row">
                                            <div class="p-dash__list-name">
                                                <span x-text="drapeau(pays.pays_code)"></span>
                                                <span class="p-text--bold" x-text="pays.pays_nom || pays.pays_code"></span>
                                            </div>
                                            <span class="p-dash__list-value" x-text="pays.visiteurs"></span>
                                        </div>
                                    </template>
                                    <template x-if="!stats?.top_pays?.length">
                                        <p class="p-dash__table-cell--center">Aucune donnée</p>
                                    </template>
                                </div>
                            </x-card>

                            <x-card titre="Appareils">
                                <div class="p-dash__devices">
                                    <div class="p-dash__devices-chart">
                                        <canvas x-ref="graphiqueAppareils"></canvas>
                                    </div>
                                    <div class="p-dash__devices-legend">
                                        <template x-for="app in stats?.appareils ?? []" :key="app.appareil">
                                            <div class="p-dash__device-row">
                                                <span class="p-dash__device-name" x-text="app.appareil"></span>
                                                <span class="p-dash__device-count" x-text="app.visiteurs"></span>
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
                        <div class="p-grid p-grid--2 p-grid--4 p-mb-3">
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
                        <x-card titre="Activité des événements" class="p-mb-3">
                            <div class="p-dash__chart--tall">
                                <canvas x-ref="graphiqueEvenements"></canvas>
                            </div>
                        </x-card>

                        <div class="p-dash__grid-2">
                            {{-- Répartition par nom + Graphique Doughnut --}}
                            <x-card titre="Répartition des types">
                                <div class="p-dash__distribution">
                                    <div class="p-dash__distribution-chart">
                                        <canvas x-ref="graphiqueEvtRepart"></canvas>
                                    </div>
                                    <div class="p-dash__distribution-table">
                                        <table class="p-dash__table">
                                            <tbody>
                                                <template x-for="(evt, index) in (stats?.evenements_par_nom ?? []).slice(0, 5)" :key="evt.nom">
                                                    <tr class="p-dash__evt-row">
                                                        <td class="p-row">
                                                            <div class="p-dash__evt-dot" :style="'background-color: ' + couleursChart[index]"></div>
                                                            <span class="p-dash__evt-name" x-text="evt.nom"></span>
                                                        </td>
                                                        <td class="p-dash__table-cell--right" x-text="evt.total"></td>
                                                        <td class="p-dash__table-cell--muted" x-text="Math.round((evt.total / totalEvenements()) * 100) + '%'"></td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </x-card>

                            {{-- Top des pages où se produisent les événements --}}
                            <x-card titre="Pages les plus interactives" :padding="false">
                                <table class="p-dash__table">
                                    <thead>
                                        <tr class="p-dash__table-header--tinted">
                                            <th>URL</th>
                                            <th class="p-dash__table-th--right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="page in (stats?.evenements_par_page ?? []).slice(0, 6)" :key="page.chemin">
                                            <tr>
                                                <td class="p-dash__table-cell--truncate" x-text="page.chemin"></td>
                                                <td class="p-dash__table-cell--mono" x-text="page.total"></td>
                                            </tr>
                                        </template>
                                        <template x-if="!stats?.evenements_par_page?.length">
                                            <tr><td colspan="2" class="p-dash__table-cell--center-xl">Aucune donnée contextuelle</td></tr>
                                        </template>
                                    </tbody>
                                </table>
                            </x-card>

                            {{-- Journal des derniers événements --}}
                            <x-card titre="Dernière activité" :padding="false" class="p-dash__col-span-2">
                                <table class="p-dash__table">
                                    <thead>
                                        <tr class="p-dash__table-header--tinted">
                                            <th>Événement</th>
                                            <th>Page</th>
                                            <th class="p-dash__table-th--right">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="evt in (stats?.derniers_evenements ?? []).slice(0, 8)" :key="evt.id">
                                            <tr>
                                                <td>
                                                    <span class="p-dash__evt-badge" x-text="evt.nom"></span>
                                                </td>
                                                <td class="p-dash__table-cell--truncate" x-text="evt.chemin" style="max-width: 250px; color: var(--gray-500);"></td>
                                                <td class="p-dash__table-cell--muted" x-text="formaterDate(evt.cree_le)"></td>
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
