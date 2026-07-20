<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display font-semibold text-lg tracking-tight text-gray-800 dark:text-gray-100 leading-tight">
                {{ __('Fila de descarte') }}
            </h2>
            <a href="{{ route('descarte.remessas.index') }}" class="text-sm text-brand-600 hover:underline dark:text-brand-400">
                {{ __('Ver remessas anteriores →') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ selecionados: [] }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-green-100 text-green-800 rounded-md dark:bg-green-900/40 dark:text-green-300">
                    {{ session('status') }}
                </div>
            @endif

            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Peças reprovadas no teste aguardando devolução ao almoxarifado. Marque as que estão indo dessa vez e gere o Anexo 7 já preenchido.') }}
            </p>

            <form id="form-remessa" action="{{ route('descarte.remessas.store') }}" method="POST" target="_blank">
                @csrf
            </form>

            <div class="flex items-center justify-between">
                @can('editar')
                    <a href="{{ route('descarte.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium shadow-sm hover:bg-brand-700">
                        {{ __('+ Nova peça') }}
                    </a>
                @endcan

                @can('editar')
                    <button type="submit" form="form-remessa" :disabled="selecionados.length === 0"
                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium shadow-sm transition"
                            :class="selecionados.length === 0
                                ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500'
                                : 'bg-copper-500 text-white hover:bg-copper-600'">
                        {{ __('Gerar Anexo 7') }} <span x-show="selecionados.length > 0">(<span x-text="selecionados.length"></span>)</span>
                    </button>
                @endcan
            </div>

            <div class="rounded-xl border border-gray-200 bg-white overflow-x-auto shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            @can('editar')
                                <th class="w-10 px-4 py-3"></th>
                            @endcan
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">{{ __('Descrição') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">{{ __('Diagnóstico') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">{{ __('Cadastrada em') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($itens as $item)
                            <tr>
                                @can('editar')
                                    <td class="px-4 py-4">
                                        <input type="checkbox" name="itens[]" value="{{ $item->id }}" form="form-remessa"
                                               x-model="selecionados"
                                               class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                                    </td>
                                @endcan
                                <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $item->descricao }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $item->diagnostico }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    @can('editar')
                                        <form action="{{ route('descarte.destroy', $item) }}" method="POST" class="inline"
                                              onsubmit="return confirm('{{ __('Remover esta peça da fila?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline dark:text-red-400">{{ __('Remover') }}</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400" colspan="5">{{ __('Fila de descarte vazia.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
