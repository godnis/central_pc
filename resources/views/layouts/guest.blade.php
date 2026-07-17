<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Central PC') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|sora:600,700|ibm-plex-mono:400,500&display=swap" rel="stylesheet" />

        <script>
            if (localStorage.getItem('tema') === 'escuro' || (!localStorage.getItem('tema') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased">
        <div class="min-h-screen lg:grid lg:grid-cols-[1.15fr_1fr]">

            {{-- Painel da marca: trilhas de circuito convergindo para o chip --}}
            <div class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-brand-950 p-12 text-white">
                <svg class="absolute inset-0 h-full w-full" viewBox="0 0 720 900" fill="none" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                    <g stroke="#6fc2a0" stroke-opacity="0.16" stroke-width="2" stroke-linecap="round">
                        <path d="M-20 120 H180 a12 12 0 0 1 12 12 V300 a12 12 0 0 0 12 12 H330" />
                        <path d="M-20 220 H120 a12 12 0 0 1 12 12 V520 a12 12 0 0 0 12 12 H300" />
                        <path d="M740 80 H560 a12 12 0 0 0 -12 12 V240 a12 12 0 0 1 -12 12 H420" />
                        <path d="M740 620 H600 a12 12 0 0 1 -12 12 V760 a12 12 0 0 1 -12 12 H360" />
                        <path d="M60 920 V740 a12 12 0 0 1 12 -12 H240 a12 12 0 0 0 12 -12 V600" />
                        <path d="M500 920 V820 a12 12 0 0 0 -12 -12 H400" />
                        <path d="M-20 420 H80 a12 12 0 0 1 12 12 V640" />
                        <path d="M680 -20 V120 a12 12 0 0 1 -12 12 H600" />
                    </g>
                    <g stroke="#c2703d" stroke-opacity="0.35" stroke-width="2" stroke-linecap="round">
                        <path d="M-20 320 H220 a12 12 0 0 1 12 12 V420 a12 12 0 0 0 12 12 H320" />
                        <path d="M740 480 H540 a12 12 0 0 0 -12 12 V560" />
                    </g>
                    <g fill="#6fc2a0" fill-opacity="0.35">
                        <circle cx="330" cy="312" r="5" />
                        <circle cx="300" cy="532" r="5" />
                        <circle cx="420" cy="252" r="5" />
                        <circle cx="360" cy="772" r="5" />
                        <circle cx="252" cy="600" r="5" />
                        <circle cx="400" cy="808" r="5" />
                        <circle cx="92" cy="640" r="5" />
                        <circle cx="600" cy="132" r="5" />
                    </g>
                    <g fill="#c2703d" fill-opacity="0.6">
                        <circle cx="320" cy="432" r="5" />
                        <circle cx="528" cy="560" r="5" />
                    </g>
                </svg>

                <div class="relative">
                    <div class="flex items-center gap-3">
                        <x-application-logo class="h-10 w-10 text-brand-300" />
                        <div>
                            <p class="font-display text-xl font-bold tracking-tight">Central PC</p>
                            <p class="font-mono text-[11px] uppercase tracking-[0.2em] text-brand-300">Inventário de hardware</p>
                        </div>
                    </div>
                </div>

                <div class="relative max-w-2xl">
                    <h1 class="font-display text-5xl font-bold leading-[1.08] tracking-tight xl:text-6xl">
                        Cada máquina<br>no seu lugar,<br>cada peça<br><span class="text-copper-400">compatível.</span>
                    </h1>
                    <p class="mt-6 max-w-md text-sm leading-relaxed text-brand-200/90">
                        Inventário de máquinas por setor, catálogo estruturado de componentes
                        e verificação automática de compatibilidade de hardware.
                    </p>
                    <p class="mt-10 font-mono text-[11px] uppercase tracking-[0.2em] text-brand-300/80">
                        ATI — Assessoria de Tecnologia da Informação
                    </p>
                </div>
            </div>

            {{-- Formulário --}}
            <div class="flex min-h-screen items-center justify-center bg-white px-6 py-12 dark:bg-gray-900 lg:min-h-0">
                <div class="w-full max-w-sm">
                    <div class="mb-10 flex items-center gap-3 lg:hidden">
                        <x-application-logo class="h-9 w-9 text-brand-600 dark:text-brand-300" />
                        <div>
                            <p class="font-display text-lg font-bold tracking-tight">Central PC</p>
                            <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-600 dark:text-brand-300">Inventário de hardware</p>
                        </div>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
