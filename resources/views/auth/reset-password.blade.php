<x-guest-layout>
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Réinitialiser le mot de passe</h2>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="space-y-4">
            <x-input
                name="email"
                label="Adresse e-mail"
                type="email"
                :required="true"
                :value="old('email', $request->email)"
                autocomplete="username"
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

        <div class="mt-6">
            <x-button variant="primary" type="submit" class="w-full justify-center">
                Réinitialiser
            </x-button>
        </div>
    </form>
</x-guest-layout>
