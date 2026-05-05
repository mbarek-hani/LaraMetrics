<x-guest-layout>
    <h2 class="p-auth__title">Mot de passe oublié</h2>
    <p class="p-auth__text">
        Entrez votre adresse e-mail et nous vous enverrons un lien de réinitialisation.
    </p>

    @if(session('status'))
        <div class="p-auth__status">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="p-auth__form-group">
            <x-input
                name="email"
                label="Adresse e-mail"
                type="email"
                :required="true"
                :value="old('email')"
                autocomplete="username"
            />
        </div>

        <div class="p-auth__submit">
            <x-button variant="primary" type="submit">
                Envoyer le lien
            </x-button>
        </div>

        <p class="p-auth__footer">
            <a href="{{ route('login') }}" class="p-auth__link">
                Retour à la connexion
            </a>
        </p>
    </form>
</x-guest-layout>
