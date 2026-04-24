<div
    x-data="aiWidget()"
    x-init="charger()"
    class="bg-white rounded-lg shadow-sm p-6 mb-6"
>
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
            Analyse IA
        </h3>

        <button
            @click="charger()"
            :disabled="chargement"
            class="text-sm text-indigo-600 hover:text-indigo-800 disabled:opacity-50 transition"
        >
            <span x-show="!chargement">Actualiser</span>
            <span x-show="chargement">Chargement...</span>
        </button>
    </div>

    {{-- État : chargement --}}
    <div x-show="chargement" class="flex items-center gap-3 text-gray-500 py-8 justify-center">
        <svg class="animate-spin h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
        </svg>
        <span>Analyse en cours...</span>
    </div>

    {{-- État : erreur --}}
    <div
        x-show="erreur"
        x-text="erreur"
        class="text-red-600 bg-red-50 border border-red-200 rounded p-3 text-sm"
    ></div>

    {{-- État : rapport disponible --}}
    <div x-show="rapport && !chargement" class="space-y-4">

        {{-- Score global --}}
        <div class="flex items-center gap-4 p-4 bg-indigo-50 rounded-lg">
            <div class="text-4xl font-bold text-indigo-600" x-text="rapport?.score + '/10'"></div>
            <div>
                <p class="font-medium text-gray-900">Score de performance</p>
                <p class="text-sm text-gray-500" x-text="rapport?.resume"></p>
            </div>
        </div>

        {{-- Points clés --}}
        <div>
            <h4 class="font-medium text-gray-700 mb-2">Points clés</h4>
            <ul class="space-y-2">
                <template x-for="point in rapport?.points_cles" :key="point">
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <span class="text-green-500 mt-0.5">✓</span>
                        <span x-text="point"></span>
                    </li>
                </template>
            </ul>
        </div>

        {{-- Recommandations --}}
        <div>
            <h4 class="font-medium text-gray-700 mb-2">Recommandations</h4>
            <ul class="space-y-2">
                <template x-for="reco in rapport?.recommandations" :key="reco">
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <span class="text-indigo-500 mt-0.5">→</span>
                        <span x-text="reco"></span>
                    </li>
                </template>
            </ul>
        </div>

        {{-- Date du rapport --}}
        <p class="text-xs text-gray-400 text-right" x-text="'Généré le ' + rapport?.genere_le"></p>
    </div>
</div>

<script>
function aiWidget() {
    return {
        chargement : false,
        rapport    : null,
        erreur     : null,

        async charger() {
            this.chargement = true;
            this.erreur     = null;

            try {
                const response = await fetch('/plugins/ai-analytics/rapport', {
                    headers: {
                        'Accept'           : 'application/json',
                        'X-Requested-With' : 'XMLHttpRequest',
                        'X-CSRF-TOKEN'     : document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                if (!response.ok) {
                    throw new Error('Erreur serveur : ' + response.status);
                }

                const data  = await response.json();
                this.rapport = data.rapport;

            } catch (e) {
                this.erreur = 'Impossible de charger le rapport : ' + e.message;
            } finally {
                this.chargement = false;
            }
        }
    }
}
</script>
