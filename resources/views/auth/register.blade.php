<x-guest-layout>
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Créer un compte</h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="space-y-4">
            <x-input
                name="name"
                label="Nom"
                type="text"
                :required="true"
                :value="old('name')"
                autocomplete="name"
            />

            <x-input
                name="email"
                label="Adresse e-mail"
                type="email"
                :required="true"
                :value="old('email')"
                autocomplete="username"
            />

            <x-input
                name="password"
                label="Mot de passe"
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

        <div class="mt-6">
            <x-button variant="primary" type="submit" class="w-full justify-center">
                Créer le compte
            </x-button>
        </div>

        <p class="mt-4 text-center text-sm text-gray-500">
            Déjà inscrit ?
            <a href="{{ route('login') }}" class="text-gray-900 font-medium hover:underline">
                Se connecter
            </a>
        </p>
    </form>
</x-guest-layout>
