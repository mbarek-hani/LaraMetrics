<div x-data="aiAnalytics()" x-init="init()">
    {{-- En-tête --}}
    <div class="p-ai__header p-mb-3">
        <div>
            <h3 class="p-section__title">Rapport d'analyse IA</h3>
            <p class="p-text--xs p-mt-sm">
                Analyse intelligente de votre trafic par intelligence artificielle.
            </p>
        </div>

        <x-button variant="primary" size="sm" @click="generer()" x-bind:disabled="generation">
            <x-custom-icon name="arrow-path" class="c-icon--xs" x-bind:class="generation ? 'animate-spin' : ''" />
            <span x-text="generation ? 'Analyse...' : 'Générer un rapport'"></span>
        </x-button>
    </div>

    {{-- Sélecteur de dates --}}
    <x-card class="p-mb-3">
        <div class="p-ai__date-row">
            <div class="u-flex-1">
                <label class="c-input-label" style="font-size: 0.75rem;">Du</label>
                <input type="date" x-model="dateDebut" :max="dateMax()" @change="validerDates()" class="c-input">
            </div>
            <div class="u-flex-1">
                <label class="c-input-label" style="font-size: 0.75rem;">Au</label>
                <input type="date" x-model="dateFin" :max="dateMax()" :min="dateDebut" @change="validerDates()"
                    class="c-input">
            </div>
            <div class="p-text--xs">
                <p x-show="!erreurDates" class="p-text--muted">
                    <span x-text="nbJours()"></span> jours analysés
                </p>
                <p class="p-text--error" x-show="erreurDates" x-text="erreurDates"></p>
            </div>
        </div>
    </x-card>

    {{-- Erreur configuration --}}
    <div x-show="erreurConfig" class="p-flash p-flash--warning p-mb-3">
        <div class="p-row">
            <x-custom-icon name="exclamation" class="c-icon--lg c-icon--warning" />
            <div>
                <p class="p-text--bold">Configuration requise</p>
                <p class="p-text--xs p-mt-sm">
                    Configurez votre clé API dans
                    <a href="{{ route('settings.index') }}" class="underline font-medium">Réglages</a>
                    pour utiliser l'analyse IA.
                </p>
            </div>
        </div>
    </div>

    {{-- Erreur API --}}
    <div x-show="erreur && !erreurConfig" class="p-flash p-flash--error p-mb-3">
        <div class="p-row">
            <x-custom-icon name="x-mark" class="c-icon--sm c-icon--error" />
            <p class="p-text" x-text="erreur"></p>
        </div>
    </div>

    {{-- Génération en cours --}}
    <div x-show="generation" class="p-mb-3">
        <x-card>
            <div class="p-ai__loading">
                <x-custom-icon name="arrow-path" class="c-icon--lg animate-spin c-icon--gray-400" />
                <div class="u-text-center">
                    <p class="p-text--bold">Analyse en cours...</p>
                    <p class="p-text--xs p-mt-sm">
                        L'IA analyse votre trafic. Cela peut prendre quelques secondes.
                    </p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Chargement initial --}}
    <div x-show="chargement && !generation" class="p-ai__loading">
        <div class="p-row">
            <x-custom-icon name="arrow-path" class="c-icon--sm animate-spin c-icon--gray-400" />
            <span class="p-text">Chargement...</span>
        </div>
    </div>

    {{-- État vide --}}
    <div x-show="!rapport && !chargement && !generation && !erreur && !erreurConfig" x-cloak>
        <x-card>
            <div class="p-ai__empty">
                <x-custom-icon name="cpu" class="c-icon--xl c-icon--gray-300" style="margin-bottom: 1rem;" />
                <h4 class="p-section__title u-text-center">Aucun rapport généré</h4>
                <p class="p-text u-text-center p-mt-sm">
                    Sélectionnez une période et cliquez sur "Générer un rapport".
                </p>
            </div>
        </x-card>
    </div>

    {{-- Rapport --}}
    <div x-show="rapport && !chargement && !generation" x-cloak class="p-stack">

        {{-- Score --}}
        <x-card>
            <div class="p-ai__report-layout">
                <div class="p-ai__score-circle p-ai__score-circle--md">
                    <span class="text-2xl font-bold" :class="{
                            'p-ai__score-text--good'    : rapport?.score >= 7,
                            'p-ai__score-text--warning' : rapport?.score >= 4 && rapport?.score < 7,
                            'p-ai__score-text--danger'  : rapport?.score < 4,
                        }" x-text="rapport?.score"></span>
                    <span class="p-text--xs">/10</span>
                </div>
                <div class="p-ai__report-content">
                    <div class="p-row p-mb-1">
                        <h4 class="p-section__title">Score de performance</h4>
                        <span class="p-badge" :class="{
                                'p-badge--success' : rapport?.score >= 7,
                                'p-badge--warning' : rapport?.score >= 4 && rapport?.score < 7,
                                'p-badge--error'   : rapport?.score < 4,
                            }" x-text="rapport?.score >= 7 ? 'Bon' : rapport?.score >= 4 ? 'Moyen' : 'Faible'"></span>
                    </div>
                    <p class="p-text p-text--muted" x-text="rapport?.resume"></p>
                </div>
            </div>
            <div class="p-ai__report-meta">
                <span x-text="'Généré le ' + rapport?.genere_le"></span>
                <span x-text="rapport?.fournisseur + ' · ' + rapport?.modele"></span>
            </div>
        </x-card>

        {{-- Points clés + Recommandations --}}
        <div class="p-grid p-grid--2 p-grid--gap-md">
            <x-card titre="Points clés">
                <ul class="p-ai__list">
                    <template x-for="(point, i) in rapport?.points_cles ?? []" :key="i">
                        <li class="p-ai__list-item">
                            <x-custom-icon name="check" class="c-icon--lg c-icon--success" />
                            <span x-text="point"></span>
                        </li>
                    </template>
                    <template x-if="!rapport?.points_cles?.length">
                        <li class="p-text--xs p-text--muted">Aucun point clé.</li>
                    </template>
                </ul>
            </x-card>

            <x-card titre="Recommandations">
                <ul class="p-ai__list">
                    <template x-for="(reco, i) in rapport?.recommandations ?? []" :key="i">
                        <li class="p-ai__list-item">
                            <span class="p-ai__reco-num" x-text="i + 1"></span>
                            <span x-text="reco"></span>
                        </li>
                    </template>
                    <template x-if="!rapport?.recommandations?.length">
                        <li class="p-text--xs p-text--muted">Aucune recommandation.</li>
                    </template>
                </ul>
            </x-card>
        </div>

        {{-- Tendances --}}
        <x-card titre="Tendances observées">
            <div class="p-grid p-grid--2 p-grid--gap-md">
                <template x-for="(tendance, i) in rapport?.tendances ?? []" :key="i">
                    <div class="p-ai__trend-card">
                        <x-custom-icon name="arrow-trending-up" class="c-icon--lg c-icon--info" />
                        <span x-text="tendance"></span>
                    </div>
                </template>
                <template x-if="!rapport?.tendances?.length">
                    <p class="p-text--xs p-text--muted">Aucune tendance identifiée.</p>
                </template>
            </div>
        </x-card>
    </div>
</div>

<script>
    function aiAnalytics() {
        return {
            // ─── Récupère le siteId du dashboard parent ───────────────
            get siteId() {
                const root = document.getElementById('dashboard-root');
                if (root && root._x_dataStack) {
                    return root._x_dataStack[0]?.siteId ?? 0;
                }
                return {{ \App\Models\Site::latest()->value('id') ?? 0 }};
            },

            // ─── État ────────────────────────────────────────────────
            chargement: false,
            generation: false,
            rapport: null,
            historique: [],
            erreur: null,
            erreurConfig: false,
            erreurDates: null,

            // ─── Dates ───────────────────────────────────────────────
            dateDebut: '',
            dateFin: '',

            // ─── Init ────────────────────────────────────────────────
            init() {
                // Initialiser les dates par défaut (30 derniers jours)
                const aujourd = new Date();
                const il_y_a = new Date();
                il_y_a.setDate(aujourd.getDate() - 30);

                this.dateFin = aujourd.toISOString().split('T')[0];
                this.dateDebut = il_y_a.toISOString().split('T')[0];

                this.chargerDernier();
            },

            // ─── Helpers dates ───────────────────────────────────────
            dateMax() {
                return new Date().toISOString().split('T')[0];
            },

            nbJours() {
                if (!this.dateDebut || !this.dateFin) return 0;
                const diff = new Date(this.dateFin) - new Date(this.dateDebut);
                return Math.max(0, Math.round(diff / 86400000) + 1);
            },

            validerDates() {
                this.erreurDates = null;

                const debut = new Date(this.dateDebut);
                const fin = new Date(this.dateFin);
                const aujourd = new Date();
                aujourd.setHours(23, 59, 59, 999);

                if (!this.dateDebut || !this.dateFin) {
                    this.erreurDates = 'Veuillez sélectionner les deux dates.';
                    return false;
                }

                if (debut > aujourd) {
                    this.erreurDates = 'La date de début ne peut pas être dans le futur.';
                    return false;
                }

                if (fin > aujourd) {
                    this.erreurDates = 'La date de fin ne peut pas être dans le futur.';
                    return false;
                }

                if (debut > fin) {
                    this.erreurDates = 'La date de début doit être avant la date de fin.';
                    return false;
                }

                if (this.nbJours() > 365) {
                    this.erreurDates = 'La période ne peut pas dépasser 365 jours.';
                    return false;
                }

                return true;
            },

            // ─── Charger le dernier rapport ──────────────────────────
            async chargerDernier() {
                if (!this.siteId) return;

                this.chargement = true;
                this.erreur = null;
                this.erreurConfig = false;

                try {
                    const r = await fetch(
                        `/plugins/ai-analytics/dernier?site_id=${this.siteId}`,
                        { headers: this.headers() }
                    );

                    if (!r.ok) throw new Error('Erreur ' + r.status);

                    const data = await r.json();
                    this.rapport = data.rapport ? { ...data.rapport, id: data.rapport.id ?? 'dernier' } : null;
                } catch (e) {
                    this.erreur = e.message;
                } finally {
                    this.chargement = false;
                }
            },

            // ─── Générer un nouveau rapport ──────────────────────────
            async generer() {
                if (this.generation) return;
                if (!this.validerDates()) return;

                this.generation = true;
                this.erreur = null;
                this.erreurConfig = false;

                try {
                    const r = await fetch('/plugins/ai-analytics/generer', {
                        method: 'POST',
                        headers: {
                            ...this.headers(),
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            site_id: this.siteId,
                            date_debut: this.dateDebut,
                            date_fin: this.dateFin,
                        }),
                    });

                    const data = await r.json();

                    if (!r.ok) {
                        if (data.erreur?.includes('Clé API') ||
                            data.erreur?.includes('non configurée')) {
                            this.erreurConfig = true;
                        } else {
                            this.erreur = data.erreur ?? 'Erreur inconnue.';
                        }
                        return;
                    }

                    this.rapport = { ...data.rapport, id: 'nouveau' };
                } catch (e) {
                    this.erreur = 'Erreur réseau : ' + e.message;
                } finally {
                    this.generation = false;
                }
            },

            // ─── Headers ────────────────────────────────────────────
            headers() {
                return {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                };
            },
        }
    }
</script>