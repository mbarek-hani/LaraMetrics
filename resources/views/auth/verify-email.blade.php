<x-guest-layout>
    <h2 class="p-auth__title">Vérification de l'e-mail</h2>
    <p class="p-auth__text">
        Merci de vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer.
    </p>

    @if(session('status') == 'verification-link-sent')
        <div class="p-auth__status">
            Un nouveau lien de vérification a été envoyé.
        </div>
    @endif

    <div class="p-auth__actions">
        <form method="POST" action="{{ route('verification.send') }}" style="margin-right: 1rem;">
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
