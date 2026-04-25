<x-card>
    <div class="mb-4 pb-3 border-b border-gray-200">
        <h3 class="text-sm font-semibold text-gray-900">Mot de passe</h3>
        <p class="text-xs text-gray-500 mt-0.5">Utilisez un mot de passe long et unique.</p>
    </div>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="space-y-4 max-w-lg">
            <x-input
                name="current_password"
                label="Mot de passe actuel"
                type="password"
                :required="true"
                autocomplete="current-password"
            />

            <x-input
                name="password"
                label="Nouveau mot de passe"
                type="password"
                :required="true"
                autocomplete="new-password"
            />

            <x-input
                name="password_confirmation"
                label="Confirmer le mot de passe"
                type="password"
                :required="true"
                autocomplete="new-password"
            />
        </div>

        <div class="mt-4 pt-3 border-t border-gray-200 flex items-center gap-3">
            <x-button variant="primary" size="sm" type="submit">
                Mettre à jour
            </x-button>

            @if(session('status') === 'password-updated')
                <span class="text-sm text-green-600" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
                    Mis à jour.
                </span>
            @endif
        </div>
    </form>
</x-card>
