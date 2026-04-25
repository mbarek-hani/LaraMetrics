<x-guest-layout>
    <h2 class="text-lg font-semibold text-gray-900 mb-2">Vérification de l'e-mail</h2>
    <p class="text-sm text-gray-500 mb-4">
        Merci de vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer.
    </p>

    @if(session('status') == 'verification-link-sent')
        <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded p-3">
            Un nouveau lien de vérification a été envoyé.
        </div>
    @endif

    <div class="flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-button variant="primary" type="submit">
                Renvoyer le lien
            </x-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-button type="submit">
                Déconnexion
            </x-button>
        </form>
    </div>
</x-guest-layout>
