<x-card>
    <div class="p-section__header">
        <h3 class="p-section__title">Informations du profil</h3>
        <p class="p-section__subtitle">Mettez à jour votre nom et adresse e-mail.</p>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="p-form-group">
            <x-input
                name="name"
                label="Nom"
                type="text"
                :required="true"
                :value="old('name', $user->name)"
                autocomplete="name"
            />

            <div>
                <x-input
                    name="email"
                    label="Adresse e-mail"
                    type="email"
                    :required="true"
                    :value="old('email', $user->email)"
                    autocomplete="username"
                />

                @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <p class="p-text--muted">
                            Votre adresse e-mail n'est pas vérifiée.
                            <button form="send-verification"
                                    class="p-text--bold" style="text-decoration: underline; cursor: pointer; background: none; border: none;">
                                Renvoyer le lien
                            </button>
                        </p>

                        @if(session('status') === 'verification-link-sent')
                            <p class="p-flash--success p-mt-1">
                                Un nouveau lien a été envoyé.
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="p-card-footer">
            <x-button variant="primary" size="sm" type="submit">
                Sauvegarder
            </x-button>

            @if(session('status') === 'profile-updated')
                <span class="p-flash--success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
                    Sauvegardé.
                </span>
            @endif
        </div>
    </form>
</x-card>
