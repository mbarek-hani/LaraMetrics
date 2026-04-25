<x-card>
    <div class="mb-4 pb-3 border-b border-gray-200">
        <h3 class="text-sm font-semibold text-gray-900">Informations du profil</h3>
        <p class="text-xs text-gray-500 mt-0.5">Mettez à jour votre nom et adresse e-mail.</p>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="space-y-4 max-w-lg">
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
                        <p class="text-sm text-gray-600">
                            Votre adresse e-mail n'est pas vérifiée.
                            <button form="send-verification"
                                    class="text-gray-900 font-medium underline hover:no-underline">
                                Renvoyer le lien
                            </button>
                        </p>

                        @if(session('status') === 'verification-link-sent')
                            <p class="mt-1 text-sm text-green-600">
                                Un nouveau lien a été envoyé.
                            </p>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-gray-200 flex items-center gap-3">
            <x-button variant="primary" size="sm" type="submit">
                Sauvegarder
            </x-button>

            @if(session('status') === 'profile-updated')
                <span class="text-sm text-green-600" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
                    Sauvegardé.
                </span>
            @endif
        </div>
    </form>
</x-card>
