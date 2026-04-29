<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LaraMetrics') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        {{-- CSS des plugins actifs --}}
        @foreach(app(\App\Core\Plugin\PluginManager::class)->getCssPaths() as $cssPath)
            <link rel="stylesheet" href="{{ asset($cssPath) }}">
        @endforeach
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div
            x-data="sidebar()"
            class="min-h-screen flex"
        >
            {{-- ══════ OVERLAY MOBILE ══════ --}}
            <div
                x-show="ouvert"
                x-transition:enter="transition-opacity ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="ouvert = false"
                class="fixed inset-0 bg-gray-900/50 z-20 lg:hidden"
                x-cloak
            ></div>

            {{-- ══════ SIDEBAR ══════ --}}
            <aside
                :class="ouvert ? 'translate-x-0' : '-translate-x-full'"
                class="fixed top-0 left-0 h-full w-64 bg-white border-r border-gray-200
                    flex flex-col z-30 transition-transform duration-200 ease-in-out"
            >
                {{-- Logo + Toggle --}}
                <div class="flex items-center justify-between px-4 h-14 border-b border-gray-200 shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-custom-icon name="chart-bar" class="w-5 h-5 text-gray-700" />
                        <span class="font-bold text-sm text-gray-900">
                            {{ config('app.name', 'LaraMetrics') }}
                        </span>
                    </a>
                    <button
                        @click="ouvert = false"
                        class="lg:hidden p-1.5 rounded hover:bg-gray-100 text-gray-500"
                    >
                        <x-custom-icon name="x-mark" class="w-4 h-4" />
                    </button>
                </div>

                {{-- Navigation principale --}}
                <nav class="flex-1 overflow-y-auto py-3">

                    {{-- Core links --}}
                    <div class="px-3 mb-1">
                        <p class="px-2 mb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Principal
                        </p>
                        @include('layouts.sidebar-link', [
                            'route' => 'dashboard',
                            'label' => 'Tableau de bord',
                            'icon'  => 'chart-bar',
                        ])
                        @include('layouts.sidebar-link', [
                            'route' => 'sites.index',
                            'label' => 'Sites',
                            'icon'  => 'globe',
                        ])
                        @include('layouts.sidebar-link', [
                            'route' => 'plugins.index',
                            'label' => 'Plugins',
                            'icon'  => 'puzzle',
                        ])
                    </div>

                    {{-- Liens ajoutés par les plugins --}}
                    @php
                        $navItems = app(\App\Core\Plugin\PluginManager::class)->getNavigationItems();
                    @endphp

                    @if(!empty($navItems))
                        <div class="px-3 mt-4 mb-1">
                            <p class="px-2 mb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Plugins
                            </p>
                            @foreach($navItems as $item)
                                <a
                                    href="{{ route($item['route']) }}"
                                    class="flex items-center gap-2.5 px-2 py-2 rounded text-sm font-medium transition
                                        {{ request()->routeIs($item['route'] . '*')
                                            ? 'bg-gray-100 text-gray-900'
                                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                                >
                                    @if(isset($item['icone']))
                                        <x-custom-icon :name="$item['icone']" class="w-4 h-4 shrink-0" />
                                    @endif
                                    {{ $item['label'] }}
                                </a>

                                {{-- Sous-menus --}}
                                @if(!empty($item['sous_menus']))
                                    <div class="ml-6 mt-0.5 space-y-0.5">
                                        @foreach($item['sous_menus'] as $sousMenu)
                                            <a
                                                href="{{ route($sousMenu['route']) }}"
                                                class="flex items-center gap-2 px-2 py-1.5 rounded text-sm transition
                                                    {{ request()->routeIs($sousMenu['route'])
                                                        ? 'text-gray-900 font-medium'
                                                        : 'text-gray-500 hover:text-gray-900' }}"
                                            >
                                                {{ $sousMenu['label'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </nav>

                {{-- Bas de sidebar --}}
                <div class="border-t border-gray-200 shrink-0">
                    {{-- Settings --}}
                    <div class="px-3 py-2">
                        @include('layouts.sidebar-link', [
                            'route' => 'settings.index',
                            'label' => 'Réglages',
                            'icon'  => 'cog',
                        ])
                    </div>

                    {{-- User --}}
                    <div class="px-3 py-3 border-t border-gray-200">
                        <div class="flex items-center gap-2.5 px-2 py-1.5">
                            <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center shrink-0">
                                <span class="text-xs font-semibold text-gray-600">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-gray-900 truncate">
                                    {{ Auth::user()->name }}
                                </p>
                                <p class="text-xs text-gray-400 truncate">
                                    {{ Auth::user()->email }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-1 space-y-0.5">
                            <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-2 px-2 py-1.5 rounded text-sm text-gray-600
                                    hover:bg-gray-50 hover:text-gray-900 transition">
                                <x-custom-icon name="users" class="w-4 h-4" />
                                Profil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2 px-2 py-1.5 rounded text-sm
                                            text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition">
                                    <x-custom-icon name="arrow-right-on-rectangle" class="w-4 h-4" />
                                    Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- ══════ CONTENU PRINCIPAL ══════ --}}
            <div
                :class="ouvert ? 'lg:ml-64' : 'lg:ml-0'"
                class="flex-1 flex flex-col min-w-0 transition-[margin] duration-200 ease-in-out"
            >

                {{-- Topbar (juste le toggle) --}}
                <div class="sticky top-0 z-10 bg-white border-b border-gray-200 h-14 flex items-center px-4 gap-3 lg:hidden">
                    <button
                        @click="ouvert = true"
                        class="p-1.5 rounded hover:bg-gray-100 text-gray-500"
                    >
                        <x-custom-icon name="bars-3" class="w-5 h-5" />
                    </button>
                    <span class="font-semibold text-sm text-gray-900">
                        {{ config('app.name', 'LaraMetrics') }}
                    </span>
                </div>

                {{-- Toggle desktop (collapsible) --}}
                <div
                    :class="ouvert ? 'left-[252px]' : 'left-0'"
                    class="hidden lg:flex fixed top-5 z-40 transition-[left] duration-200 ease-in-out"
                >
                    <button
                        @click="ouvert = !ouvert"
                        class="w-6 h-6 flex items-center justify-center
                            bg-white border border-gray-200 rounded shadow-sm
                            text-gray-400 hover:text-gray-700 hover:bg-gray-50 transition"
                        :title="ouvert ? 'Réduire le menu' : 'Ouvrir le menu'"
                    >
                        <span x-show="ouvert">
                            <x-custom-icon name="chevron-left" class="w-3.5 h-3.5" />
                        </span>
                        <span x-show="!ouvert" x-cloak>
                            <x-custom-icon name="chevron-right" class="w-3.5 h-3.5" />
                        </span>
                    </button>
                </div>
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('scripts')
        <script>
            function sidebar() {
                return {
                    ouvert: window.innerWidth >= 1024,
                }
            }
        </script>
    </body>
</html>
