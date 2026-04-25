<x-card>
    <div class="mb-4 pb-3 border-b border-gray-200">
        <h3 class="text-sm font-semibold text-gray-900">Supprimer le compte</h3>
        <p class="text-xs text-gray-500 mt-0.5">Cette action est irréversible. Toutes vos données seront supprimées.</p>
    </div>

    <div x-data="{ confirmSuppression: false }">
        <x-button variant="danger" size="sm" @click="confirmSuppression = true">
            <x-icon name="trash" class="w-3.5 h-3.5" />
            Supprimer mon compte
        </x-button>

        {{-- Modal de confirmation --}}
        <div
            x-show="confirmSuppression"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50"
            @click.self="confirmSuppression = false"
            @keydown.escape.window="confirmSuppression = false"
        >
            <div
                x-show="confirmSuppression"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-white border border-gray-200 rounded p-6 w-full max-w-md mx-4"
            >
                <h3 class="text-sm font-semibold text-gray-900">Confirmer la suppression</h3>
                <p class="text-sm text-gray-500 mt-2">
                    Entrez votre mot de passe pour confirmer la suppression définitive de votre compte.
                </p>

                <form method="post" action="{{ route('profile.destroy') }}" class="mt-4">
                    @csrf
                    @method('delete')

                    <x-input
                        name="password"
                        label="Mot de passe"
                        type="password"
                        :required="true"
                        placeholder="Votre mot de passe"
                    />

                    <div class="mt-4 flex items-center justify-end gap-2">
                        <x-button type="button" size="sm" @click="confirmSuppression = false">
                            Annuler
                        </x-button>
                        <x-button variant="danger" size="sm" type="submit">
                            Supprimer définitivement
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-card>
