<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Comunhão Espírita') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/fivecom-icon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|sora:600,700|ibm-plex-mono:400,500&display=swap" rel="stylesheet" />

        <!-- Tema escuro: aplicado antes do Alpine carregar, pra não piscar claro->escuro -->
        <script>
            if (localStorage.getItem('tema') === 'escuro' || (!localStorage.getItem('tema') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 dark:text-gray-100">
        <a href="#conteudo-principal" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:p-3 focus:bg-white focus:text-gray-900">
            {{ __('Pular para o conteúdo') }}
        </a>
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 lg:flex">
            @include('layouts.navigation')

            <div class="min-w-0 flex-1">
                <!-- Page Heading -->
                @isset($header)
                    <header class="border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                        <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main id="conteudo-principal">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
