<x-app-layout>
    <x-slot name="titre">
        Réglages
    </x-slot>
    <div class="p-page">
        <div class="p-container p-container--sm p-stack">
            <h2 class="p-page__title">Réglages</h2>
            {{-- Réglages Core (toujours visible) --}}
            <x-card>
                <div class="p-section__header--with-icon">
                    <x-custom-icon name="cog" class="p-section__icon" />
                    <h4 class="p-section__title">Flux</h4>
                    <span class="p-section__badge">Core</span>
                </div>
                <p class="p-text">
                    Aucun réglage core pour le moment.
                    Les réglages des plugins actifs apparaissent ci-dessous.
                </p>
            </x-card>

            {{-- Réglages des plugins --}}
            @forelse($reglages as $pluginId => $plugin)
                <x-card x-data="reglagesPlugin('{{ $pluginId }}', {{ json_encode($valeurs[$pluginId] ?? []) }})">
                    <div class="p-section__header--with-icon">
                        <x-custom-icon name="puzzle" class="p-section__icon" />
                        <h4 class="p-section__title">{{ $plugin['nom'] }}</h4>
                        <span class="p-section__badge">Plugin</span>
                    </div>

                    <form @submit.prevent="sauvegarder" class="p-form-group">
                        @foreach($plugin['champs'] as $champ)
                            <div>
                                <label class="c-input-label">
                                    {{ $champ['label'] }}
                                    @if($champ['obligatoire'] ?? false)
                                        <span class="c-input-required">*</span>
                                    @endif
                                </label>

                                @if(($champ['type'] ?? 'text') === 'select')
                                    <select x-model="valeurs['{{ $champ['cle'] }}']" class="c-input" {{ ($champ['obligatoire'] ?? false) ? 'required' : '' }}>
                                        @foreach($champ['options'] ?? [] as $option)
                                            <option value="{{ $option }}">{{ ucfirst($option) }}</option>
                                        @endforeach
                                    </select>
                                @elseif(($champ['type'] ?? 'text') === 'textarea')
                                    <textarea x-model="valeurs['{{ $champ['cle'] }}']" rows="3" class="c-input" {{ ($champ['obligatoire'] ?? false) ? 'required' : '' }}></textarea>
                                @else
                                    <input type="{{ $champ['type'] ?? 'text' }}" x-model="valeurs['{{ $champ['cle'] }}']"
                                        placeholder="{{ $champ['placeholder'] ?? '' }}" class="c-input" {{ ($champ['obligatoire'] ?? false) ? 'required' : '' }}>
                                @endif

                                @if(isset($champ['aide']))
                                    <p class="c-input-help">{{ $champ['aide'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </form>

                    <div class="p-card-footer">
                        <x-button variant="primary" size="sm" @click="$el.closest('.c-card').querySelector('form').requestSubmit()" x-bind:disabled="sauvegarde">
                            <span x-text="sauvegarde ? 'Sauvegarde...' : 'Sauvegarder'"></span>
                        </x-button>
                        <span x-show="succes" x-transition class="p-row p-flash--success">
                            <x-custom-icon name="check" class="c-icon--sm" />
                            Sauvegardé
                        </span>
                        <span x-show="erreur" x-transition class="p-text" style="color: var(--error-accent);" x-text="erreur"></span>
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
                    valeurs: valeursInitiales || {},
                    sauvegarde: false,
                    succes: false,
                    erreur: null,

                    async sauvegarder() {
                        this.sauvegarde = true;
                        this.succes = false;
                        this.erreur = null;

                        try {
                            const donnees = Object.assign({}, this.valeurs);
                            const r = await fetch('/settings/reglages', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: JSON.stringify({
                                    plugin: pluginId,
                                    reglages: donnees,
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