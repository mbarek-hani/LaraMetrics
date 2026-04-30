<x-app-layout>
    <div class="py-4">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">{{ $site->nom }}</h2>
                <x-button href="{{ route('sites.index') }}">
                    Retour
                </x-button>
            </div>

            {{-- Infos du site --}}
            <x-card titre="Informations">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Domaine</span>
                        <p class="font-medium text-gray-900">{{ $site->domaine }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Statut</span>
                        <p class="font-medium {{ $site->actif ? 'text-green-700' : 'text-gray-500' }}">
                            {{ $site->actif ? 'Actif' : 'Inactif' }}
                        </p>
                    </div>
                    <div>
                        <span class="text-gray-500">Créé le</span>
                        <p class="font-medium text-gray-900">{{ $site->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Token</span>
                        <p class="font-mono text-xs text-gray-600 break-all">{{ $site->token_tracking }}</p>
                    </div>
                </div>
            </x-card>

            {{-- Script de tracking --}}
            <x-card titre="Script de tracking">
                <p class="text-sm text-gray-500 mb-3">
                    Copiez ce code et collez-le juste avant la balise <code class="text-xs bg-gray-100 px-1 py-0.5 rounded">&lt;/head&gt;</code> de votre site.
                </p>

                <div
                    x-data="{ copie: false }"
                    class="relative"
                >
                    <pre class="bg-gray-100 border border-gray-200 rounded p-3 text-xs font-mono text-gray-800 overflow-x-auto"><code>{{ $site->getScriptTracking() }}</code></pre>

                   <button
                        @click="
                            navigator.clipboard.writeText($refs.code.textContent);
                            copie = true;
                            setTimeout(() => copie = false, 2000);
                        "
                        class="absolute top-2 right-2 p-1.5 rounded border border-gray-300 bg-white hover:bg-gray-50 transition"
                    >
                        <span x-show="!copie">
                            <x-custom-icon name="clipboard" class="w-4 h-4 text-gray-500" />
                        </span>

                        <span x-show="copie" x-cloak>
                            <x-custom-icon name="check" class="w-4 h-4 text-green-600" />
                        </span>
                    </button>

                    <span x-ref="code" class="hidden">{{ $site->getScriptTracking() }}</span>
                </div>
            </x-card>

        </div>
    </div>
</x-app-layout>
