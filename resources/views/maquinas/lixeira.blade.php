<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">{{ __('Lixeira de máquinas') }}</h2>
            <a href="{{ route('maquinas.index') }}" class="text-sm text-gray-600 hover:underline">{{ __('Voltar') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-x-auto shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Nome') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Setor') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Excluída em') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($maquinas as $maquina)
                            <tr>
                                <td class="px-6 py-4">{{ $maquina->nome }}</td>
                                <td class="px-6 py-4">{{ $maquina->setor->nome }}</td>
                                <td class="px-6 py-4">{{ $maquina->deleted_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <form action="{{ route('maquinas.restaurar', $maquina->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-indigo-600 hover:underline">{{ __('Restaurar') }}</button>
                                    </form>
                                    <form action="{{ route('maquinas.excluirDefinitivamente', $maquina->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('{{ __('Excluir definitivamente? Essa ação não pode ser desfeita.') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">{{ __('Excluir definitivamente') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 text-gray-500" colspan="4">{{ __('Lixeira vazia.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
