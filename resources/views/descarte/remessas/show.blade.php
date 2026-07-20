<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Anexo 7 — Remessa') }} #{{ $remessa->id }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600|sora:600,700|ibm-plex-mono:400,500&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @page { size: A4 landscape; margin: 12mm; }

        .anexo-tabela th, .anexo-tabela td {
            border: 1px solid #444;
        }

        .anexo-pagina {
            page-break-after: always;
            break-after: page;
        }

        .anexo-pagina:last-child {
            page-break-after: auto;
            break-after: auto;
        }

        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
</head>
<body class="font-sans text-gray-900 bg-gray-100 print:bg-white">

    <div class="no-print sticky top-0 z-10 flex items-center justify-between bg-brand-950 px-6 py-3 text-white">
        <p class="text-sm">{{ __('Remessa') }} #{{ $remessa->id }} — {{ $remessa->created_at->format('d/m/Y H:i') }}</p>
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-copper-500 px-4 py-2 text-sm font-medium text-white hover:bg-copper-600">
            {{ __('Imprimir') }}
        </button>
    </div>

    @foreach ($paginas as $pagina)
        <section class="anexo-pagina mx-auto max-w-5xl bg-white p-8 print:p-0">
            <div class="mb-4 flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="" class="h-12 w-auto">
                    <div>
                        <p class="font-display text-lg font-bold leading-tight">{{ __('ALMOXARIFADO') }}</p>
                        <p class="max-w-md text-[11px] leading-snug text-gray-600">
                            {{ __('Entrada e saída de móveis, equipamentos elétrico/eletrônicos e de informática para avaliação e destinação final') }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-mono text-xs uppercase tracking-wider text-gray-500">{{ __('Anexo 7') }}</p>
                    <p class="font-mono text-[10px] text-gray-400">{{ __('Remessa') }} #{{ $remessa->id }} · {{ $remessa->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            <table class="anexo-tabela w-full border-collapse text-[10px]">
                <thead>
                    <tr class="bg-gray-100">
                        <th rowspan="2" class="px-1 py-1 w-6">{{ __('Nº') }}</th>
                        <th rowspan="2" class="px-2 py-1 text-left">{{ __('Descrição do material') }}</th>
                        <th rowspan="2" class="px-2 py-1 w-20">{{ __('Retirado por') }}</th>
                        <th rowspan="2" class="px-2 py-1 w-16">{{ __('Data da retirada') }}</th>
                        <th rowspan="2" class="px-2 py-1 w-24">{{ __('Devolvido por') }}</th>
                        <th rowspan="2" class="px-2 py-1 w-16">{{ __('Data da devolução') }}</th>
                        <th rowspan="2" class="px-2 py-1 text-left w-32">{{ __('Diagnóstico') }}</th>
                        <th colspan="2" class="px-2 py-1">{{ __('Destinação final') }}</th>
                    </tr>
                    <tr class="bg-gray-100">
                        <th class="px-2 py-1 w-20">{{ __('Local') }}</th>
                        <th class="px-2 py-1 w-16">{{ __('Data') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pagina as $indice => $item)
                        <tr class="h-6">
                            <td class="px-1 py-1 text-center">{{ $indice + 1 }}</td>
                            <td class="px-2 py-1">{{ $item?->descricao }}</td>
                            <td class="px-2 py-1"></td>
                            <td class="px-2 py-1"></td>
                            <td class="px-2 py-1">{{ $item ? $remessa->devolvido_por : '' }}</td>
                            <td class="px-2 py-1">{{ $item ? $remessa->created_at->format('d/m/Y') : '' }}</td>
                            <td class="px-2 py-1">{{ $item?->diagnostico }}</td>
                            <td class="px-2 py-1"></td>
                            <td class="px-2 py-1"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @endforeach

</body>
</html>
