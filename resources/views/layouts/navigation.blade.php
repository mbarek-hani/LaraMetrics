<nav class="bg-white border-b border-gray-200" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14">

            {{-- Left side --}}
            <div class="flex items-center gap-6">

                {{-- Logo --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-gray-900">
                    <x-icon name="chart-bar" class="w-5 h-5" />
                    <span class="font-bold text-sm">{{ config('app.name', 'LaraMetrics') }}</span>
                </a>

                {{-- Desktop nav links --}}
                <div class="hidden sm:flex items-center gap-1">
                    <a href="{{ route('dashboard') }}"
                       class="px-3 py-1.5 text-sm font-medium rounded transition
                              {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Tableau de bord
                    </a>
                    <a href="{{ route('sites.index') }}"
                       class="px-3 py-1.5 text-sm font-medium rounded transition
                              {{ request()->routeIs('sites.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Sites
                    </a>
                    <a href="{{ route('plugins.index') }}"
                       class="px-3 py-1.5 text-sm font-medium rounded transition
                              {{ request()->routeIs('plugins.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Plugins
                    </a>
                </div>
            </div>

            {{-- Right side: user dropdown --}}
            <div class="hidden sm:flex items-center">
                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        @click.outside="open = false"
                        class="flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-gray-600 hover:text-gray-900 rounded hover:bg-gray-50 transition"
                    >
                        <span>{{ Auth::user()->name }}</span>
                        <x-icon name="x-mark" class="w-3 h-3 transition"
                                x-bind:class="open ? 'rotate-45' : 'rotate-0'" />
                    </button>

                    {{-- Dropdown --}}
                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        x-cloak
                        class="absolute right-0 mt-1 w-48 bg-white border border-gray-200 rounded py-1 z-50"
                    >
                        <a href="{{ route('profile.edit') }}"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                            Profil
                        </a>

                        <div class="border-t border-gray-100 my-1"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Mobile hamburger --}}
            <div class="flex items-center sm:hidden">
                <button
                    @click="open = !open"
                    class="p-2 rounded text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition"
                >
                    <x-icon x-show="!open" name="chart-bar" class="w-5 h-5" />
                    <x-icon x-show="open" name="x-mark" class="w-5 h-5" />
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="open" x-cloak class="sm:hidden border-t border-gray-200">
        <div class="py-2 px-4 space-y-1">
            <a href="{{ route('dashboard') }}"
               class="block px-3 py-2 text-sm font-medium rounded transition
                      {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
                Tableau de bord
            </a>
            <a href="{{ route('sites.index') }}"
               class="block px-3 py-2 text-sm font-medium rounded transition
                      {{ request()->routeIs('sites.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
                Sites
            </a>
            <a href="{{ route('plugins.index') }}"
               class="block px-3 py-2 text-sm font-medium rounded transition
                      {{ request()->routeIs('plugins.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50' }}">
                Plugins
            </a>
        </div>

        <div class="py-2 px-4 border-t border-gray-100">
            <p class="px-3 text-xs text-gray-400 font-medium">{{ Auth::user()->name }}</p>

            <a href="{{ route('profile.edit') }}"
               class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded transition">
                Profil
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="block w-full text-left px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded transition">
                    Déconnexion
                </button>
            </form>
        </div>
    </div>
</nav>
