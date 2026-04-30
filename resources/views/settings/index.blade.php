<x-app-layout>
    <x-slot name="titre">
        Réglages
    </x-slot>
    <div class="py-4">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Réglages</h2>
            {{-- Réglages Core (toujours visible) --}}
            <x-card>
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                    <x-custom-icon name="cog" class="w-4 h-4 text-gray-500" />
                    <h4 class="text-sm font-semibold text-gray-900">LaraMetrics</h4>
                    <span class="text-xs text-gray-400 ml-auto">Core</span>
                </div>
                <p class="text-sm text-gray-500">
                    Aucun réglage core pour le moment.
                    Les réglages des plugins actifs apparaissent ci-dessous.
                </p>
            </x-card>

            {{-- Réglages des plugins --}}
            @forelse($reglages as $pluginId => $plugin)
                <x-card x-data="reglagesPlugin('{{ $pluginId }}', {{ json_encode($valeurs[$pluginId] ?? []) }})">
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-200">
                        <x-custom-icon name="puzzle" class="w-4 h-4 text-gray-500" />
                        <h4 class="text-sm font-semibold text-gray-900">{{ $plugin['nom'] }}</h4>
                        <span class="text-xs text-gray-400 ml-auto">Plugin</span>
                    </div>

                    <div class="space-y-4 max-w-lg">
                        @foreach($plugin['champs'] as $champ)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ $champ['label'] }}
                                    @if($champ['obligatoire'] ?? false)
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>

                                @if(($champ['type'] ?? 'text') === 'select')
                                    <select
                                        x-model="valeurs['{{ $champ['cle'] }}']"
                                        class="block w-full rounded border-gray-300 text-sm
                                               focus:border-blue-500 focus:ring-blue-500"
                                    >
                                        @foreach($champ['options'] ?? [] as $option)
                                            <option value="{{ $option }}">{{ ucfirst($option) }}</option>
                                        @endforeach
                                    </select>
                                @elseif(($champ['type'] ?? 'text') === 'textarea')
                                    <textarea
                                        x-model="valeurs['{{ $champ['cle'] }}']"
                                        rows="3"
                                        class="block w-full rounded border-gray-300 text-sm
                                               focus:border-blue-500 focus:ring-blue-500"
                                    ></textarea>
                                @else
                                    <input
                                        type="{{ $champ['type'] ?? 'text' }}"
                                        x-model="valeurs['{{ $champ['cle'] }}']"
                                        placeholder="{{ $champ['placeholder'] ?? '' }}"
                                        class="block w-full rounded border-gray-300 text-sm
                                               focus:border-blue-500 focus:ring-blue-500"
                                    >
                                @endif

                                @if(isset($champ['aide']))
                                    <p class="mt-1 text-xs text-gray-400">{{ $champ['aide'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-200 flex items-center gap-3">
                        <x-button
                            variant="primary"
                            size="sm"
                            @click="sauvegarder()"
                            x-bind:disabled="sauvegarde"
                        >
                            <span x-text="sauvegarde ? 'Sauvegarde...' : 'Sauvegarder'"></span>
                        </x-button>
                        <span
                            x-show="succes"
                            x-transition
                            class="flex items-center gap-1 text-sm text-green-600"
                        >
                            <x-custom-icon name="check" class="w-4 h-4" />
                            Sauvegardé
                        </span>
                        <span
                            x-show="erreur"
                            x-transition
                            class="text-sm text-red-600"
                            x-text="erreur"
                        ></span>
                    </div>
                </x-card>
            @empty
                {{-- Aucun plugin avec réglages --}}
            @endforelse

        </div>
    </div>

    @push('scripts')
    <script>
    function reglagesPlugin(pluginId, valeursInitiales) {
        return {
            valeurs    : valeursInitiales || {},
            sauvegarde : false,
            succes     : false,
            erreur     : null,

            async sauvegarder() {
                this.sauvegarde = true;
                this.succes     = false;
                this.erreur     = null;

                try {
                    const donnees = Object.assign({}, this.valeurs);
                    const r = await fetch('/settings/reglages', {
                        method  : 'POST',
                        headers : {
                            'Content-Type' : 'application/json',
                            'Accept'       : 'application/json',
                            'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            plugin   : pluginId,
                            reglages : donnees,
                        }),
                    });

                    const data = await r.json();

                    if (r.ok) {
                        this.succes = true;
                        setTimeout(() => this.succes = false, 3000);
                    } else {
                        this.erreur = data.erreur ?? 'Erreur inconnue.';
                    }
                } catch (e) {
                    this.erreur = e.message;
                } finally {
                    this.sauvegarde = false;
                }
            },
        }
    }
    </script>
    @endpush
</x-app-layout>
