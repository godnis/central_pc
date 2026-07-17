@php
    $cpu = $maquina->componentesDaCategoria(\App\Enums\CategoriaComponente::Cpu)->first();
    $placaMae = $maquina->componentesDaCategoria(\App\Enums\CategoriaComponente::PlacaMae)->first();
    $rams = $maquina->componentesDaCategoria(\App\Enums\CategoriaComponente::Ram);
    $armazenamentos = $maquina->componentesDaCategoria(\App\Enums\CategoriaComponente::Armazenamento);
    $gpu = $maquina->componentesDaCategoria(\App\Enums\CategoriaComponente::Gpu)->first();
    $fonte = $maquina->componentesDaCategoria(\App\Enums\CategoriaComponente::Fonte)->first();
    $gabinete = $maquina->componentesDaCategoria(\App\Enums\CategoriaComponente::Gabinete)->first();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display font-semibold text-lg tracking-tight text-gray-800 dark:text-gray-100 leading-tight">{{ $maquina->nome }}</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('maquinas.edit', $maquina) }}" class="text-sm text-brand-600 hover:underline">{{ __('Editar') }}</a>
                <a href="{{ route('maquinas.index') }}" class="text-sm text-gray-600 hover:underline">{{ __('Voltar') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-green-100 text-green-800 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="sm:col-span-2 space-y-2 text-sm">
                    <p><span class="text-gray-500">{{ __('Patrimônio') }}:</span> {{ $maquina->patrimonio ?? '—' }}</p>
                    <p><span class="text-gray-500">{{ __('Setor') }}:</span> {{ $maquina->setor->nome }}</p>
                    <p><span class="text-gray-500">{{ __('Status') }}:</span> {{ $maquina->status->label() }}</p>
                    <p><span class="text-gray-500">{{ __('Sistema operacional') }}:</span> {{ $maquina->sistema_operacional ?? '—' }}</p>
                    <p><span class="text-gray-500">{{ __('Responsável') }}:</span> {{ $maquina->responsavel ?? '—' }}</p>
                    <p><span class="text-gray-500">{{ __('Data de aquisição') }}:</span> {{ optional($maquina->data_aquisicao)->format('d/m/Y') ?? '—' }}</p>
                    <p><span class="text-gray-500">{{ __('Observações') }}:</span> {{ $maquina->observacoes ?? '—' }}</p>
                </div>
                <div class="flex flex-col items-center gap-2">
                    @if ($maquina->foto_path)
                        <img src="{{ Storage::url($maquina->foto_path) }}" alt="" class="h-32 w-32 object-cover rounded-md border">
                    @endif
                    <img src="{{ route('maquinas.qrcode', $maquina) }}" alt="{{ __('QR code de identificação') }}" class="h-28 w-28 border rounded-md p-1">
                    <span class="text-xs text-gray-400">{{ __('QR code de identificação') }}</span>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ __('Hardware') }}</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div><dt class="text-gray-500">{{ __('Processador') }}</dt><dd>{{ $cpu?->nome ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Placa-mãe') }}</dt><dd>{{ $placaMae?->nome ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('RAM') }}</dt><dd>{{ $rams->pluck('nome')->join(', ') ?: '—' }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Armazenamento') }}</dt><dd>{{ $armazenamentos->pluck('nome')->join(', ') ?: '—' }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Placa de vídeo') }}</dt><dd>{{ $gpu?->nome ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Fonte') }}</dt><dd>{{ $fonte?->nome ?? '—' }}</dd></div>
                    <div><dt class="text-gray-500">{{ __('Gabinete') }}</dt><dd>{{ $gabinete?->nome ?? '—' }}</dd></div>
                </dl>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ __('Histórico') }}</h3>
                <ul class="space-y-2 text-sm">
                    @forelse ($maquina->atividades as $atividade)
                        <li class="border-b last:border-0 pb-2">
                            <span class="text-gray-800">{{ $atividade->descricao }}</span>
                            <span class="text-gray-400"> — {{ $atividade->user?->name ?? __('sistema') }}, {{ $atividade->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                    @empty
                        <li class="text-gray-500">{{ __('Nenhum registro de histórico ainda.') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
