<x-app-layout>
    <x-slot name="header">
        <h2 class="text-lg font-semibold text-gray-900">Profil</h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @include('profile.partials.update-profile-information-form')
            @include('profile.partials.update-password-form')
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
