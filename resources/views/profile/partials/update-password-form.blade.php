<x-card>
    <div class="p-section__header">
        <h3 class="p-section__title">Mot de passe</h3>
        <p class="p-section__subtitle">Utilisez un mot de passe long et unique.</p>
    </div>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="p-form-group">
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

        <div class="p-card-footer">
            <x-button variant="primary" size="sm" type="submit">
                Mettre à jour
            </x-button>

            @if(session('status') === 'password-updated')
                <span class="p-flash--success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
                    Mis à jour.
                </span>
            @endif
        </div>
    </form>
</x-card>
