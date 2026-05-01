<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Flux') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 px-4">

        <a href="/" class="mb-6 flex items-center gap-2 text-gray-900">
            <x-application-logo class="h-12 w-auto" />
        </a>

        <div class="w-full sm:max-w-md bg-gray-50 border border-gray-200 rounded p-6">
            {{ $slot }}
        </div>

        <p class="mt-6 text-xs text-gray-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'Flux') }}
        </p>
    </div>
</body>

</html>