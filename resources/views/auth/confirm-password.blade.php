<x-guest-layout>
    <h2 class="p-auth__title">Confirmation requise</h2>
    <p class="p-auth__text">
        Veuillez confirmer votre mot de passe avant de continuer.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="p-auth__form-group">
            <x-input
                name="password"
                label="Mot de passe"
                type="password"
                :required="true"
                autocomplete="current-password"
            />
        </div>

        <div class="p-auth__submit">
            <x-button variant="primary" type="submit">
                Confirmer
            </x-button>
        </div>
    </form>
</x-guest-layout>
