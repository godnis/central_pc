<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Máquinas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Cards de resumo --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <p class="text-sm text-gray-500">{{ __('Total de máquinas') }}</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $totalGeral }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 sm:col-span-2 min-w-0">
                    <p class="text-sm text-gray-500 mb-2">{{ __('Total por setor') }}</p>
                    <div class="flex flex-wrap gap-2 min-w-0">
                        @foreach ($totalPorSetor as $setor)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-700">
                                {{ $setor->nome }}: {{ $setor->maquinas_count }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Filtro e ação --}}
            <div class="flex items-center justify-between">
                <form action="{{ route('maquinas.index') }}" method="GET" class="flex items-center gap-2">
                    <select name="setor_id" class="rounded-md border-gray-300 text-sm" onchange="this.form.submit()">
                        <option value="">{{ __('Todos os setores') }}</option>
                        @foreach ($setores as $setor)
                            <option value="{{ $setor->id }}" @selected($setorId == $setor->id)>{{ $setor->nome }}</option>
                        @endforeach
                    </select>
                </form>

                <a href="{{ route('maquinas.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-sm hover:bg-gray-700">
                    {{ __('Nova máquina') }}
                </a>
            </div>

            {{-- Tabela --}}
            <div class="bg-white overflow-x-auto shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Nome') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Setor') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('SO') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Processador') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Placa-mãe') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('RAM') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Armazenamento') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Observações') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($maquinas as $maquina)
                            @php
                                $cpu = $maquina->componentesDaCategoria(\App\Enums\CategoriaComponente::Cpu)->first();
                                $placaMae = $maquina->componentesDaCategoria(\App\Enums\CategoriaComponente::PlacaMae)->first();
                                $rams = $maquina->componentesDaCategoria(\App\Enums\CategoriaComponente::Ram);
                                $armazenamentos = $maquina->componentesDaCategoria(\App\Enums\CategoriaComponente::Armazenamento);
                            @endphp
                            <tr>
                                <td class="px-6 py-4">{{ $maquina->nome }}</td>
                                <td class="px-6 py-4">{{ $maquina->setor->nome }}</td>
                                <td class="px-6 py-4">{{ $maquina->sistema_operacional ?? '—' }}</td>
                                <td class="px-6 py-4">{{ $cpu?->nome ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    @if ($placaMae)
                                        {{ $placaMae->nome }}
                                    @else
                                        <span class="text-amber-600">{{ __('a definir') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">{{ $rams->pluck('nome')->join(', ') ?: '—' }}</td>
                                <td class="px-6 py-4">{{ $armazenamentos->pluck('nome')->join(', ') ?: '—' }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $maquina->observacoes ?? '—' }}</td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('maquinas.edit', $maquina) }}" class="text-indigo-600 hover:underline">{{ __('Editar') }}</a>
                                    <form action="{{ route('maquinas.destroy', $maquina) }}" method="POST" class="inline"
                                          onsubmit="return confirm('{{ __('Excluir esta máquina?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">{{ __('Excluir') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 text-gray-500" colspan="9">{{ __('Nenhuma máquina cadastrada.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
