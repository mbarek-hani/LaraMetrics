<!-- resources/views/components/application-logo.blade.php -->
<div {{ $attributes->merge(['class' => 'flex items-center gap-1']) }}>
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-10 h-10">
        <path d="M4 17L10 11L14 15L20 9" stroke="currentColor" stroke-width="3" stroke-linecap="round"
            stroke-linejoin="round" class="text-emerald-500" />
        <path d="M16 9H20V13" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
            class="text-emerald-500" />
    </svg>

    <!-- Texte FLUX -->
    <span class="text-2xl font-black tracking-tighter text-emerald-500 uppercase select-none">
        Flux
    </span>
</div>