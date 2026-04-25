<x-guest-layout>
    <h2 class="text-lg font-semibold text-gray-900 mb-2">Confirmation requise</h2>
    <p class="text-sm text-gray-500 mb-4">
        Veuillez confirmer votre mot de passe avant de continuer.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <x-input
            name="password"
            label="Mot de passe"
            type="password"
            :required="true"
            autocomplete="current-password"
        />

        <div class="mt-6">
            <x-button variant="primary" type="submit" class="w-full justify-center">
                Confirmer
            </x-button>
        </div>
    </form>
</x-guest-layout>
