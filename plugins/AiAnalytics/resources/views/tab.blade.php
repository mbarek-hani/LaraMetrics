<div x-data="aiTab()">
    <x-card>
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Rapport d'analyse IA</h3>
                <p class="text-xs text-gray-500 mt-0.5">Analyse automatique de votre trafic.</p>
            </div>
            <x-button variant="primary" size="sm" @click="generer()" x-bind:disabled="chargement">
                <x-icon x-show="!chargement" name="arrow-path" class="w-3.5 h-3.5" />
                <x-icon x-show="chargement" name="arrow-path" class="w-3.5 h-3.5 animate-spin" />
                <span x-text="chargement ? 'Analyse...' : 'Générer'"></span>
            </x-button>
        </div>

        {{-- Erreur --}}
        <div x-show="erreur" class="bg-red-50 border border-red-200 rounded p-3 mb-4">
            <p class="text-sm text-red-700" x-text="erreur"></p>
        </div>

        {{-- État initial --}}
        <div x-show="!rapport && !chargement && !erreur" class="text-center py-10">
            <x-icon name="cpu" class="w-10 h-10 text-gray-300 mx-auto" />
            <p class="mt-2 text-sm text-gray-500">Cliquez sur "Générer" pour lancer l'analyse.</p>
        </div>

        {{-- Rapport --}}
        <div x-show="rapport" class="space-y-4">
            <div class="flex items-center gap-4 bg-gray-100 rounded p-4">
                <div class="text-2xl font-bold text-gray-900" x-text="rapport?.score + '/10'"></div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Score de performance</p>
                    <p class="text-xs text-gray-500 mt-0.5" x-text="rapport?.resume"></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Points clés</h4>
                    <ul class="space-y-1.5">
                        <template x-for="point in rapport?.points_cles ?? []" :key="point">
                            <li class="flex items-start gap-2 text-sm text-gray-700">
                                <x-icon name="check" class="w-3.5 h-3.5 text-green-600 mt-0.5 shrink-0" />
                                <span x-text="point"></span>
                            </li>
                        </template>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Recommandations</h4>
                    <ul class="space-y-1.5">
                        <template x-for="reco in rapport?.recommandations ?? []" :key="reco">
                            <li class="flex items-start gap-2 text-sm text-gray-700">
                                <span class="text-gray-400 mt-0.5 shrink-0">—</span>
                                <span x-text="reco"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            <p class="text-xs text-gray-400 text-right pt-2 border-t border-gray-200" x-text="'Généré le ' + rapport?.genere_le"></p>
        </div>
    </x-card>
</div>

<script>
function aiTab() {
    return {
        chargement: false,
        rapport: null,
        erreur: null,
        async generer() {
            this.chargement = true;
            this.erreur = null;
            try {
                const r = await fetch('/plugins/ai-analytics/rapport', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                });
                if (!r.ok) throw new Error('Erreur ' + r.status);
                this.rapport = (await r.json()).rapport;
            } catch (e) {
                this.erreur = e.message;
            } finally {
                this.chargement = false;
            }
        },
    }
}
</script>
