<x-app-layout>
    <div class="py-4">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Profil</h2>
            @include('profile.partials.update-profile-information-form')
            @include('profile.partials.update-password-form')
        </div>
    </div>
</x-app-layout>
