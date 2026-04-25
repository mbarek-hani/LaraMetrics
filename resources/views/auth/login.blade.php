<x-guest-layout>
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Connexion</h2>

    @if(session('status'))
        <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded p-3">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="space-y-4">
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
                autocomplete="current-password"
            />

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember"
                           class="rounded border-gray-300 text-blue-500 focus:ring-blue-500">
                    <span class="text-sm text-gray-600">Se souvenir de moi</span>
                </label>

                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-sm text-gray-500 hover:text-gray-700 transition">
                        Mot de passe oublié ?
                    </a>
                @endif
            </div>
        </div>

        <div class="mt-6">
            <x-button variant="primary" type="submit" class="w-full justify-center">
                Se connecter
            </x-button>
        </div>

        @if(Route::has('register'))
            <p class="mt-4 text-center text-sm text-gray-500">
                Pas encore de compte ?
                <a href="{{ route('register') }}" class="text-gray-900 font-medium hover:underline">
                    Créer un compte
                </a>
            </p>
        @endif
    </form>
</x-guest-layout>
