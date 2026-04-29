<div x-data="aiAnalytics()" x-init="chargerDernier()">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div>
            <h3 class="text-sm font-semibold text-gray-900">Rapport d'analyse IA</h3>
            <p class="text-xs text-gray-500 mt-0.5">
                Analyse automatique de votre trafic par intelligence artificielle.
            </p>
        </div>

        <div class="flex items-center gap-2">

            <select
                x-model="siteId"
                @change="chargerDernier()"
                class="rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
            >
                @foreach(\App\Models\Site::actifs()->orderBy('nom')->get() as $site)
                    <option value="{{ $site->id }}">{{ $site->nom }}</option>
                @endforeach
            </select>

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
                <span x-text="generation ? 'Analyse...' : 'Générer'"></span>
            </x-button>
        </div>
    </div>

    {{-- Erreur de configuration --}}
    <div
        x-show="erreurConfig"
        class="bg-amber-50 border border-amber-200 rounded p-4 mb-4"
    >
        <div class="flex items-start gap-2">
            <x-custom-icon name="exclamation" class="w-4 h-4 text-amber-600 mt-0.5 shrink-0" />
            <div>
                <p class="text-sm font-medium text-amber-800">Configuration requise</p>
                <p class="text-xs text-amber-700 mt-1">
                    Configurez votre clé API dans l'onglet
                    <strong>Réglages</strong> pour utiliser l'analyse IA.
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

    {{-- Chargement --}}
    <div x-show="chargement && !generation" class="flex items-center justify-center py-12">
        <x-custom-icon name="arrow-path" class="w-5 h-5 text-gray-400 animate-spin" />
        <span class="ml-2 text-sm text-gray-500">Chargement...</span>
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

    {{-- État initial : aucun rapport --}}
    <div
        x-show="!rapport && !chargement && !generation && !erreur && !erreurConfig"
        x-cloak
    >
        <x-card>
            <div class="text-center py-10">
                <x-custom-icon name="cpu" class="w-10 h-10 text-gray-300 mx-auto" />
                <h4 class="mt-3 text-sm font-semibold text-gray-900">Aucun rapport généré</h4>
                <p class="mt-1 text-sm text-gray-500 max-w-sm mx-auto">
                    Cliquez sur "Générer" pour lancer votre première analyse IA.
                </p>
            </div>
        </x-card>
    </div>

    {{-- Rapport --}}
    <div x-show="rapport && !chargement && !generation" x-cloak class="space-y-4">

        {{-- Score + résumé --}}
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
                    <div class="flex items-center gap-2 mb-1">
                        <h4 class="text-sm font-semibold text-gray-900">
                            Score de performance
                        </h4>
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

            <div class="mt-3 pt-3 border-t border-gray-200 flex items-center justify-between text-xs text-gray-400">
                <span x-text="'Généré le ' + rapport?.genere_le"></span>
                <span x-text="rapport?.fournisseur + ' · ' + rapport?.modele"></span>
            </div>
        </x-card>

        {{-- Points clés + Recommandations --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Points clés --}}
            <x-card titre="Points clés">
                <ul class="space-y-2">
                    <template x-for="(point, i) in rapport?.points_cles ?? []" :key="i">
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <x-custom-icon name="check" class="w-4 h-4 text-green-600 mt-0.5 shrink-0" />
                            <span x-text="point"></span>
                        </li>
                    </template>
                    <template x-if="!rapport?.points_cles?.length">
                        <li class="text-sm text-gray-400">Aucun point clé identifié.</li>
                    </template>
                </ul>
            </x-card>

            {{-- Recommandations --}}
            <x-card titre="Recommandations">
                <ul class="space-y-2">
                    <template x-for="(reco, i) in rapport?.recommandations ?? []" :key="i">
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="text-gray-400 mt-0.5 shrink-0 font-mono text-xs"
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

        {{-- Historique --}}
        <x-card titre="Historique des rapports" :padding="false">
            <div x-show="historique.length === 0" class="px-4 py-6 text-center text-sm text-gray-400">
                Aucun rapport précédent.
            </div>
            <table class="w-full text-sm" x-show="historique.length > 0">
                <thead>
                    <tr class="border-b border-gray-200 text-left">
                        <th class="px-4 py-2 text-xs font-medium text-gray-500">Date</th>
                        <th class="px-4 py-2 text-xs font-medium text-gray-500 text-center">Score</th>
                        <th class="px-4 py-2 text-xs font-medium text-gray-500">Résumé</th>
                        <th class="px-4 py-2 text-xs font-medium text-gray-500">Modèle</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="h in historique" :key="h.id">
                        <tr class="border-b border-gray-100 hover:bg-gray-50 cursor-pointer"
                            @click="rapport = h">
                            <td class="px-4 py-2 text-gray-500 text-xs whitespace-nowrap"
                                x-text="h.genere_le"></td>
                            <td class="px-4 py-2 text-center">
                                <span
                                    class="inline-block w-8 text-center text-xs font-bold px-1.5 py-0.5 rounded"
                                    :class="{
                                        'bg-green-100 text-green-700' : h.score >= 7,
                                        'bg-amber-100 text-amber-700' : h.score >= 4 && h.score < 7,
                                        'bg-red-100 text-red-700'     : h.score < 4,
                                    }"
                                    x-text="h.score + '/10'"
                                ></span>
                            </td>
                            <td class="px-4 py-2 text-gray-700 truncate max-w-[250px]"
                                x-text="h.resume"></td>
                            <td class="px-4 py-2 text-gray-400 text-xs"
                                x-text="h.fournisseur + ' · ' + h.modele"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </x-card>
    </div>
</div>

<script>
function aiAnalytics() {
    return {
        siteId      : {{ \App\Models\Site::latest()->value('id') ?? 0 }},
        chargement  : false,
        generation  : false,
        rapport     : null,
        historique  : [],
        erreur      : null,
        erreurConfig: false,

        async chargerDernier() {
            if (!this.siteId) return;

            this.chargement  = true;
            this.erreur      = null;
            this.erreurConfig = false;
            this.rapport     = null;

            try {
                const r = await fetch(
                    `/plugins/ai-analytics/dernier?site_id=${this.siteId}`,
                    { headers: this.headers() }
                );

                if (!r.ok) throw new Error('Erreur ' + r.status);

                const data    = await r.json();
                this.rapport  = data.rapport;

                // Charger l'historique en parallèle
                this.chargerHistorique();

            } catch (e) {
                this.erreur = e.message;
            } finally {
                this.chargement = false;
            }
        },

        async generer() {
            if (!this.siteId || this.generation) return;

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
                    body: JSON.stringify({ site_id: this.siteId }),
                });

                const data = await r.json();

                if (!r.ok) {
                    if (data.erreur?.includes('Clé API')) {
                        this.erreurConfig = true;
                    } else {
                        this.erreur = data.erreur ?? 'Erreur inconnue.';
                    }
                    return;
                }

                this.rapport = data.rapport;
                this.chargerHistorique();

            } catch (e) {
                this.erreur = 'Erreur réseau : ' + e.message;
            } finally {
                this.generation = false;
            }
        },

        async chargerHistorique() {
            try {
                const r = await fetch(
                    `/plugins/ai-analytics/historique?site_id=${this.siteId}`,
                    { headers: this.headers() }
                );

                if (r.ok) {
                    const data     = await r.json();
                    this.historique = data.rapports ?? [];
                }
            } catch {
                // Silencieux — l'historique est optionnel
            }
        },


        headers() {
            return {
                'Accept'       : 'application/json',
                'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content,
            };
        },
    }
}
</script>
