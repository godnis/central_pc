<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
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
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <p class="text-sm text-gray-500">{{ __('Total de máquinas') }}</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $totalGeral }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">{{ __('Por status') }}</p>
                    <div class="flex flex-wrap gap-1">
                        @foreach ($statusList as $statusOpcao)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700">
                                {{ $statusOpcao->label() }}: {{ $totalPorStatus[$statusOpcao->value] ?? 0 }}
                            </span>
                        @endforeach
                    </div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <p class="text-sm text-gray-500">{{ __('Idade média do parque') }}</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ $idadeMediaAnos !== null ? $idadeMediaAnos.' anos' : '—' }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-4 min-w-0">
                    <p class="text-sm text-gray-500 mb-2">{{ __('Total por setor') }}</p>
                    <div class="flex flex-wrap gap-1 min-w-0 max-h-16 overflow-y-auto">
                        @foreach ($totalPorSetor as $setor)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700">
                                {{ $setor->nome }}: {{ $setor->maquinas_count }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Filtro, busca e ações --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <form action="{{ route('maquinas.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <input type="text" name="busca" value="{{ $busca }}" placeholder="{{ __('Buscar por nome, patrimônio, SO, componente…') }}"
                           class="rounded-md border-gray-300 text-sm w-64">
                    <select name="setor_id" class="rounded-md border-gray-300 text-sm" onchange="this.form.submit()">
                        <option value="">{{ __('Todos os setores') }}</option>
                        @foreach ($setores as $setor)
                            <option value="{{ $setor->id }}" @selected($setorId == $setor->id)>{{ $setor->nome }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="rounded-md border-gray-300 text-sm" onchange="this.form.submit()">
                        <option value="">{{ __('Todos os status') }}</option>
                        @foreach ($statusList as $statusOpcao)
                            <option value="{{ $statusOpcao->value }}" @selected($status == $statusOpcao->value)>{{ $statusOpcao->label() }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-1.5 bg-gray-100 rounded-md text-sm hover:bg-gray-200">{{ __('Filtrar') }}</button>
                </form>

                <div class="flex items-center gap-3">
                    <a href="{{ route('maquinas.export', request()->query()) }}" class="text-sm text-gray-600 hover:underline">{{ __('Exportar CSV') }}</a>
                    @can('excluir')
                        <a href="{{ route('maquinas.lixeira') }}" class="text-sm text-gray-600 hover:underline">{{ __('Lixeira') }}</a>
                    @endcan
                    @can('editar')
                        <a href="{{ route('maquinas.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-sm hover:bg-gray-700">
                            {{ __('Nova máquina') }}
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Tabela --}}
            <div class="bg-white overflow-x-auto shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @php
                                $colunas = ['nome' => 'Nome', 'patrimonio' => 'Patrimônio', 'status' => 'Status'];
                            @endphp
                            @foreach ($colunas as $campo => $rotulo)
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    <a href="{{ route('maquinas.index', array_merge(request()->query(), ['sort' => $campo, 'dir' => $sort === $campo && $dir === 'asc' ? 'desc' : 'asc'])) }}" class="hover:underline">
                                        {{ __($rotulo) }}
                                        @if ($sort === $campo) {{ $dir === 'asc' ? '↑' : '↓' }} @endif
                                    </a>
                                </th>
                            @endforeach
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Setor') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('SO') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Processador') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Placa-mãe') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($maquinas as $maquina)
                            @php
                                $cpu = $maquina->componentesDaCategoria(\App\Enums\CategoriaComponente::Cpu)->first();
                                $placaMae = $maquina->componentesDaCategoria(\App\Enums\CategoriaComponente::PlacaMae)->first();
                            @endphp
                            <tr>
                                <td class="px-6 py-4">
                                    <a href="{{ route('maquinas.show', $maquina) }}" class="text-indigo-600 hover:underline">{{ $maquina->nome }}</a>
                                </td>
                                <td class="px-6 py-4">{{ $maquina->patrimonio ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <span @class([
                                        'inline-flex items-center px-2 py-0.5 rounded-full text-xs',
                                        'bg-green-100 text-green-700' => $maquina->status->value === 'ativa',
                                        'bg-amber-100 text-amber-700' => $maquina->status->value === 'manutencao',
                                        'bg-gray-200 text-gray-700' => $maquina->status->value === 'baixada',
                                    ])>{{ $maquina->status->label() }}</span>
                                </td>
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
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    @can('editar')
                                        <a href="{{ route('maquinas.edit', $maquina) }}" class="text-indigo-600 hover:underline">{{ __('Editar') }}</a>
                                    @endcan
                                    @can('excluir')
                                        <form action="{{ route('maquinas.destroy', $maquina) }}" method="POST" class="inline"
                                              onsubmit="return confirm('{{ __('Excluir esta máquina?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">{{ __('Excluir') }}</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 text-gray-500" colspan="8">{{ __('Nenhuma máquina encontrada.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $maquinas->links() }}
        </div>
    </div>
</x-app-layout>
