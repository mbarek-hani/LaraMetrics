<x-guest-layout>
    <h2 class="text-lg font-semibold text-gray-900 mb-2">Mot de passe oublié</h2>
    <p class="text-sm text-gray-500 mb-4">
        Entrez votre adresse e-mail et nous vous enverrons un lien de réinitialisation.
    </p>

    @if(session('status'))
        <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded p-3">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <x-input
            name="email"
            label="Adresse e-mail"
            type="email"
            :required="true"
            :value="old('email')"
            autocomplete="username"
        />

        <div class="mt-6">
            <x-button variant="primary" type="submit" class="w-full justify-center">
                Envoyer le lien
            </x-button>
        </div>

        <p class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
                Retour à la connexion
            </a>
        </p>
    </form>
</x-guest-layout>
