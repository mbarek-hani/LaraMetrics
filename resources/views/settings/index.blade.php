<x-app-layout>
    <x-slot name="titre">
        Réglages
    </x-slot>
    <div class="p-page">
        <div class="p-container p-container--sm p-stack">
            <h2 class="p-page__title">Réglages</h2>
            {{-- Réglages Core (toujours visible) --}}
            <x-card x-data="themeSwitcher()">
                <div class="p-section__header--with-icon">
                    <x-custom-icon name="swatch" class="p-section__icon" />
                    <h4 class="p-section__title">Apparence</h4>
                    <span class="p-section__badge">Core</span>
                </div>
                
                <p class="p-text">Choisissez le thème de l'application :</p>
                
                <div class="c-theme-switcher">
                    <!-- Light Mode Button -->
                    <button type="button" class="c-theme-card" :class="{ 'c-theme-card--active': theme === 'light' }" @click="setTheme('light')">
                        <div class="c-theme-preview c-theme-preview--light">
                            <div class="c-theme-preview__header"></div>
                            <div class="c-theme-preview__line"></div>
                            <div class="c-theme-preview__line c-theme-preview__line--short"></div>
                        </div>
                        <span class="p-text--bold" style="font-size: 0.875rem;">Clair</span>
                    </button>
                    
                    <!-- Dark Mode Button -->
                    <button type="button" class="c-theme-card" :class="{ 'c-theme-card--active': theme === 'dark' }" @click="setTheme('dark')">
                        <div class="c-theme-preview c-theme-preview--dark">
                            <div class="c-theme-preview__header"></div>
                            <div class="c-theme-preview__line"></div>
                            <div class="c-theme-preview__line c-theme-preview__line--short"></div>
                        </div>
                        <span class="p-text--bold" style="font-size: 0.875rem;">Sombre</span>
                    </button>
                </div>
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

                        <div x-show="succes" x-cloak>
                            <template x-if="succes">
                                <x-alert type="succes" message="Réglages sauvegardés avec succès." />
                            </template>
                        </div>

                        <div x-show="erreur" x-cloak>
                            <template x-if="erreur">
                                <x-alert type="erreur">
                                    <span x-text="erreur"></span>
                                </x-alert>
                            </template>
                        </div>
                    </div>
                </x-card>
            @empty
                {{-- Aucun plugin avec réglages --}}
            @endforelse

        </div>
    </div>

    @push('scripts')
        <script>
            function themeSwitcher() {
                return {
                    theme: localStorage.getItem('theme') || 'light',
                    init() {
                        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                            this.theme = 'dark';
                        }
                    },
                    setTheme(value) {
                        this.theme = value;
                        localStorage.setItem('theme', value);
                        if (value === 'dark') {
                            document.documentElement.setAttribute('data-theme', 'dark');
                        } else {
                            document.documentElement.removeAttribute('data-theme');
                        }
                    }
                }
            }

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