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

<body class="l-body">
    <div class="l-guest">

        <a href="/" class="l-guest__logo-link">
            <x-application-logo />
        </a>

        <div class="l-guest__card">
            {{ $slot }}
        </div>

        <p class="l-guest__footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'Flux') }}
        </p>
    </div>
</body>

</html>