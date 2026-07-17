@php
    $itemBase = 'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition';
    $itemAtivo = $itemBase.' bg-brand-800 text-white shadow-[inset_2px_0_0_0_#c2703d]';
    $itemInativo = $itemBase.' text-brand-200/80 hover:bg-brand-900 hover:text-white';
@endphp

<div x-data="{ sidebar: false, escuro: document.documentElement.classList.contains('dark') }" class="contents">

    {{-- Barra superior (mobile) --}}
    <div class="sticky top-0 z-40 flex h-14 items-center justify-between bg-brand-950 px-4 text-white lg:hidden">
        <a href="{{ route('maquinas.index') }}" class="flex items-center gap-2">
            <x-application-logo class="h-7 w-7 text-brand-300" />
            <span class="font-display text-base font-bold tracking-tight">Central PC</span>
        </a>
        <button @click="sidebar = true" class="rounded-md p-2 text-brand-200 hover:bg-brand-900 hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-500" aria-label="{{ __('Abrir menu') }}">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
        </button>
    </div>

    {{-- Overlay (mobile) --}}
    <div x-show="sidebar" x-transition.opacity @click="sidebar = false" class="fixed inset-0 z-40 bg-black/50 lg:hidden" style="display: none;"></div>

    {{-- Sidebar --}}
    <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 flex w-64 shrink-0 -translate-x-full transform flex-col bg-brand-950 text-white transition-transform duration-200 ease-in-out lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">

        <div class="flex h-16 items-center justify-between px-5">
            <a href="{{ route('maquinas.index') }}" class="flex items-center gap-2.5">
                <x-application-logo class="h-8 w-8 text-brand-300" />
                <div>
                    <span class="font-display text-base font-bold tracking-tight leading-none">Central PC</span>
                    <span class="block font-mono text-[9px] uppercase tracking-[0.2em] text-brand-300/80">Inventário</span>
                </div>
            </a>
            <button @click="sidebar = false" class="rounded-md p-1.5 text-brand-300 hover:bg-brand-900 hover:text-white lg:hidden" aria-label="{{ __('Fechar menu') }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-4">
            <div>
                <p class="px-3 pb-2 font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400/70">{{ __('Inventário') }}</p>
                <div class="space-y-1">
                    <a href="{{ route('maquinas.index') }}" class="{{ request()->routeIs('maquinas.*') ? $itemAtivo : $itemInativo }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" /></svg>
                        {{ __('Máquinas') }}
                    </a>
                    <a href="{{ route('setores.index') }}" class="{{ request()->routeIs('setores.*') ? $itemAtivo : $itemInativo }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" /></svg>
                        {{ __('Setores') }}
                    </a>
                    <a href="{{ route('componentes.index') }}" class="{{ request()->routeIs('componentes.*') ? $itemAtivo : $itemInativo }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25zm.75-12h9v9h-9v-9z" /></svg>
                        {{ __('Componentes') }}
                    </a>
                </div>
            </div>

            @canany(['excluir'])
            <div>
                <p class="px-3 pb-2 font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400/70">{{ __('Administração') }}</p>
                <div class="space-y-1">
                    <a href="{{ route('tokens.index') }}" class="{{ request()->routeIs('tokens.*') ? $itemAtivo : $itemInativo }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" /></svg>
                        {{ __('Tokens de API') }}
                    </a>
                    <a href="{{ route('maquinas.lixeira') }}" class="{{ request()->routeIs('maquinas.lixeira') ? $itemAtivo : $itemInativo }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                        {{ __('Lixeira') }}
                    </a>
                </div>
            </div>
            @endcanany
        </nav>

        <div class="border-t border-brand-900 px-3 py-4">
            <button type="button"
                    @click="escuro = ! escuro; document.documentElement.classList.toggle('dark', escuro); localStorage.setItem('tema', escuro ? 'escuro' : 'claro');"
                    class="{{ $itemInativo }} w-full">
                <svg x-show="!escuro" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" /></svg>
                <svg x-show="escuro" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" /></svg>
                <span x-text="escuro ? '{{ __('Modo claro') }}' : '{{ __('Modo escuro') }}'"></span>
            </button>

            <div class="mt-3 flex items-center justify-between gap-2 rounded-lg bg-brand-900/60 px-3 py-2.5">
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                    <p class="truncate font-mono text-[10px] uppercase tracking-wider text-brand-300/80">{{ Auth::user()->role->label() }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-md p-1.5 text-brand-300 hover:bg-brand-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-brand-500" title="{{ __('Sair') }}" aria-label="{{ __('Sair') }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>
</div>
