<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Catálogo de componentes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-green-100 text-green-800 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex items-center justify-between">
                <form action="{{ route('componentes.index') }}" method="GET" class="flex items-center gap-2">
                    <select name="categoria" class="rounded-md border-gray-300 text-sm" onchange="this.form.submit()">
                        <option value="">{{ __('Todas as categorias') }}</option>
                        @foreach ($categorias as $cat)
                            <option value="{{ $cat->value }}" @selected($categoriaFiltro == $cat->value)>{{ $cat->label() }}</option>
                        @endforeach
                    </select>
                </form>

                <a href="{{ route('componentes.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-sm hover:bg-gray-700">
                    {{ __('Novo componente') }}
                </a>
            </div>

            <div class="bg-white overflow-x-auto shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Categoria') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Nome') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Fabricante') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($componentes as $componente)
                            <tr>
                                <td class="px-6 py-4">{{ $componente->categoria->label() }}</td>
                                <td class="px-6 py-4">{{ $componente->nome }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $componente->fabricante ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    @if ($componente->ativo)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">{{ __('Ativo') }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-600">{{ __('Inativo') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('componentes.edit', $componente) }}" class="text-indigo-600 hover:underline">{{ __('Editar') }}</a>
                                    <form action="{{ route('componentes.destroy', $componente) }}" method="POST" class="inline"
                                          onsubmit="return confirm('{{ __('Excluir este componente?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">{{ __('Excluir') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 text-gray-500" colspan="5">{{ __('Nenhum componente cadastrado.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
