<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Setores') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            @can('editar')
                <div class="mb-4">
                    <a href="{{ route('setores.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-sm hover:bg-gray-700">
                        {{ __('Novo setor') }}
                    </a>
                </div>
            @endcan

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Nome') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($setores as $setor)
                            <tr>
                                <td class="px-6 py-4">{{ $setor->nome }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    @can('editar')
                                        <a href="{{ route('setores.edit', $setor) }}" class="text-indigo-600 hover:underline">{{ __('Editar') }}</a>
                                    @endcan
                                    @can('excluir')
                                        <form action="{{ route('setores.destroy', $setor) }}" method="POST" class="inline"
                                              onsubmit="return confirm('{{ __('Excluir este setor?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">{{ __('Excluir') }}</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 text-gray-500" colspan="2">{{ __('Nenhum setor cadastrado.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
