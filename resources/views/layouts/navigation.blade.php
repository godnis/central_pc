<nav x-data="{ open: false, escuro: document.documentElement.classList.contains('dark') }" class="bg-white border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('maquinas.index') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('maquinas.index')" :active="request()->routeIs('maquinas.*')">
                        {{ __('Máquinas') }}
                    </x-nav-link>
                    <x-nav-link :href="route('setores.index')" :active="request()->routeIs('setores.*')">
                        {{ __('Setores') }}
                    </x-nav-link>
                    <x-nav-link :href="route('componentes.index')" :active="request()->routeIs('componentes.*')">
                        {{ __('Componentes') }}
                    </x-nav-link>
                    @can('excluir')
                        <x-nav-link :href="route('tokens.index')" :active="request()->routeIs('tokens.*')">
                            {{ __('Tokens de API') }}
                        </x-nav-link>
                    @endcan
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                <button type="button"
                        @click="escuro = ! escuro; document.documentElement.classList.toggle('dark', escuro); localStorage.setItem('tema', escuro ? 'escuro' : 'claro');"
                        class="p-2 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        :aria-label="escuro ? '{{ __('Ativar modo claro') }}' : '{{ __('Ativar modo escuro') }}'"
                        :title="escuro ? '{{ __('Ativar modo claro') }}' : '{{ __('Ativar modo escuro') }}'">
                    <span x-show="!escuro" aria-hidden="true">🌙</span>
                    <span x-show="escuro" aria-hidden="true">☀️</span>
                </button>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('maquinas.index')" :active="request()->routeIs('maquinas.*')">
                {{ __('Máquinas') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('setores.index')" :active="request()->routeIs('setores.*')">
                {{ __('Setores') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('componentes.index')" :active="request()->routeIs('componentes.*')">
                {{ __('Componentes') }}
            </x-responsive-nav-link>
            @can('excluir')
                <x-responsive-nav-link :href="route('tokens.index')" :active="request()->routeIs('tokens.*')">
                    {{ __('Tokens de API') }}
                </x-responsive-nav-link>
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <button type="button"
                        @click="escuro = ! escuro; document.documentElement.classList.toggle('dark', escuro); localStorage.setItem('tema', escuro ? 'escuro' : 'claro');"
                        class="block w-full text-start px-4 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <span x-show="!escuro">🌙 {{ __('Modo escuro') }}</span>
                    <span x-show="escuro">☀️ {{ __('Modo claro') }}</span>
                </button>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
