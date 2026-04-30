<div
    x-data="aiAnalytics()"
    x-init="init()"
>
    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
        <div>
            <h3 class="text-sm font-semibold text-gray-900">Rapport d'analyse IA</h3>
            <p class="text-xs text-gray-500 mt-0.5">
                Analyse intelligente de votre trafic par intelligence artificielle.
            </p>
        </div>

        <x-button
            variant="primary"
            size="sm"
            @click="generer()"
            x-bind:disabled="generation"
        >
            <x-custom-icon
                name="arrow-path"
                class="w-3.5 h-3.5"
                x-bind:class="generation ? 'animate-spin' : ''"
            />
            <span x-text="generation ? 'Analyse...' : 'Générer un rapport'"></span>
        </x-button>
    </div>

    {{-- Sélecteur de dates --}}
    <x-card class="mb-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Du</label>
                <input
                    type="date"
                    x-model="dateDebut"
                    :max="dateMax()"
                    @change="validerDates()"
                    class="block w-full rounded border-gray-300 text-sm
                           focus:border-blue-500 focus:ring-blue-500"
                >
            </div>
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Au</label>
                <input
                    type="date"
                    x-model="dateFin"
                    :max="dateMax()"
                    :min="dateDebut"
                    @change="validerDates()"
                    class="block w-full rounded border-gray-300 text-sm
                           focus:border-blue-500 focus:ring-blue-500"
                >
            </div>
            <div class="shrink-0">
                <p class="text-xs text-gray-400" x-show="!erreurDates">
                    <span x-text="nbJours()"></span> jours analysés
                </p>
                <p class="text-xs text-red-500" x-show="erreurDates" x-text="erreurDates"></p>
            </div>
        </div>
    </x-card>

    {{-- Erreur configuration --}}
    <div x-show="erreurConfig" class="bg-amber-50 border border-amber-200 rounded p-4 mb-4">
        <div class="flex items-start gap-2">
            <x-custom-icon name="exclamation" class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" />
            <div>
                <p class="text-sm font-medium text-amber-800">Configuration requise</p>
                <p class="text-xs text-amber-700 mt-1">
                    Configurez votre clé API dans
                    <a href="{{ route('settings.index') }}" class="underline font-medium">Réglages</a>
                    pour utiliser l'analyse IA.
                </p>
            </div>
        </div>
    </div>

    {{-- Erreur API --}}
    <div
        x-show="erreur && !erreurConfig"
        class="bg-red-50 border border-red-200 rounded p-3 mb-4"
    >
        <div class="flex items-start gap-2">
            <x-custom-icon name="x-mark" class="w-4 h-4 text-red-600 mt-0.5 shrink-0" />
            <p class="text-sm text-red-700" x-text="erreur"></p>
        </div>
    </div>

    {{-- Génération en cours --}}
    <div x-show="generation" class="mb-4">
        <x-card>
            <div class="flex flex-col items-center justify-center py-8 gap-3">
                <x-custom-icon name="arrow-path" class="w-8 h-8 text-gray-400 animate-spin" />
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-900">Analyse en cours...</p>
                    <p class="text-xs text-gray-500 mt-1">
                        L'IA analyse votre trafic. Cela peut prendre quelques secondes.
                    </p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Chargement initial --}}
    <div x-show="chargement && !generation" class="flex items-center justify-center py-12">
        <x-custom-icon name="arrow-path" class="w-5 h-5 text-gray-400 animate-spin" />
        <span class="ml-2 text-sm text-gray-500">Chargement...</span>
    </div>

    {{-- État vide --}}
    <div
        x-show="!rapport && !chargement && !generation && !erreur && !erreurConfig"
        x-cloak
    >
        <x-card>
            <div class="text-center py-10">
                <x-custom-icon name="cpu" class="w-10 h-10 text-gray-300 mx-auto" />
                <h4 class="mt-3 text-sm font-semibold text-gray-900">Aucun rapport généré</h4>
                <p class="mt-1 text-sm text-gray-500">
                    Sélectionnez une période et cliquez sur "Générer un rapport".
                </p>
            </div>
        </x-card>
    </div>

    {{-- Rapport --}}
    <div x-show="rapport && !chargement && !generation" x-cloak class="space-y-4">

        {{-- Score --}}
        <x-card>
            <div class="flex items-start gap-4">
                <div class="shrink-0 flex flex-col items-center justify-center
                            w-16 h-16 rounded bg-gray-100 border border-gray-200">
                    <span
                        class="text-2xl font-bold"
                        :class="{
                            'text-green-700' : rapport?.score >= 7,
                            'text-amber-600' : rapport?.score >= 4 && rapport?.score < 7,
                            'text-red-600'   : rapport?.score < 4,
                        }"
                        x-text="rapport?.score"
                    ></span>
                    <span class="text-xs text-gray-400">/10</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h4 class="text-sm font-semibold text-gray-900">Score de performance</h4>
                        <span
                            class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium border"
                            :class="{
                                'bg-green-50 text-green-700 border-green-200' : rapport?.score >= 7,
                                'bg-amber-50 text-amber-700 border-amber-200' : rapport?.score >= 4 && rapport?.score < 7,
                                'bg-red-50 text-red-700 border-red-200'       : rapport?.score < 4,
                            }"
                            x-text="rapport?.score >= 7 ? 'Bon' : rapport?.score >= 4 ? 'Moyen' : 'Faible'"
                        ></span>
                    </div>
                    <p class="text-sm text-gray-600" x-text="rapport?.resume"></p>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-200 flex flex-wrap items-center
                        justify-between gap-2 text-xs text-gray-400">
                <span x-text="'Généré le ' + rapport?.genere_le"></span>
                <span x-text="rapport?.fournisseur + ' · ' + rapport?.modele"></span>
            </div>
        </x-card>

        {{-- Points clés + Recommandations --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-card titre="Points clés">
                <ul class="space-y-2">
                    <template x-for="(point, i) in rapport?.points_cles ?? []" :key="i">
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <x-custom-icon name="check" class="w-4 h-4 text-green-600 mt-0.5 shrink-0" />
                            <span x-text="point"></span>
                        </li>
                    </template>
                    <template x-if="!rapport?.points_cles?.length">
                        <li class="text-sm text-gray-400">Aucun point clé.</li>
                    </template>
                </ul>
            </x-card>

            <x-card titre="Recommandations">
                <ul class="space-y-2">
                    <template x-for="(reco, i) in rapport?.recommandations ?? []" :key="i">
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="text-gray-400 shrink-0 font-mono text-xs mt-0.5"
                                  x-text="(i + 1) + '.'"></span>
                            <span x-text="reco"></span>
                        </li>
                    </template>
                    <template x-if="!rapport?.recommandations?.length">
                        <li class="text-sm text-gray-400">Aucune recommandation.</li>
                    </template>
                </ul>
            </x-card>
        </div>

        {{-- Tendances --}}
        <x-card titre="Tendances observées">
            <ul class="space-y-2">
                <template x-for="(tendance, i) in rapport?.tendances ?? []" :key="i">
                    <li class="flex items-start gap-2 text-sm text-gray-700">
                        <x-custom-icon name="arrow-trending-down" class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" />
                        <span x-text="tendance"></span>
                    </li>
                </template>
                <template x-if="!rapport?.tendances?.length">
                    <li class="text-sm text-gray-400">Aucune tendance identifiée.</li>
                </template>
            </ul>
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
        chargement  : false,
        generation  : false,
        rapport     : null,
        historique  : [],
        erreur      : null,
        erreurConfig: false,
        erreurDates : null,

        // ─── Dates ───────────────────────────────────────────────
        dateDebut: '',
        dateFin  : '',

        // ─── Init ────────────────────────────────────────────────
        init() {
            // Initialiser les dates par défaut (30 derniers jours)
            const aujourd = new Date();
            const il_y_a  = new Date();
            il_y_a.setDate(aujourd.getDate() - 30);

            this.dateFin   = aujourd.toISOString().split('T')[0];
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

            const debut     = new Date(this.dateDebut);
            const fin       = new Date(this.dateFin);
            const aujourd   = new Date();
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

            this.chargement  = true;
            this.erreur      = null;
            this.erreurConfig = false;

            try {
                const r = await fetch(
                    `/plugins/ai-analytics/dernier?site_id=${this.siteId}`,
                    { headers: this.headers() }
                );

                if (!r.ok) throw new Error('Erreur ' + r.status);

                const data   = await r.json();
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

            this.generation  = true;
            this.erreur      = null;
            this.erreurConfig = false;

            try {
                const r = await fetch('/plugins/ai-analytics/generer', {
                    method  : 'POST',
                    headers : {
                        ...this.headers(),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        site_id    : this.siteId,
                        date_debut : this.dateDebut,
                        date_fin   : this.dateFin,
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
                'Accept'       : 'application/json',
                'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content,
            };
        },
    }
}
</script>
