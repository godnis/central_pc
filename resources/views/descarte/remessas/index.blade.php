<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display font-semibold text-lg tracking-tight text-gray-800 dark:text-gray-100 leading-tight">
                {{ __('Remessas de descarte') }}
            </h2>
            <a href="{{ route('descarte.index') }}" class="text-sm text-gray-600 hover:underline dark:text-gray-300">{{ __('Voltar para a fila') }}</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-xl border border-gray-200 bg-white overflow-x-auto shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">{{ __('Data da devolução') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">{{ __('Devolvido por') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase dark:text-gray-400">{{ __('Peças') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($remessas as $remessa)
                            <tr>
                                <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $remessa->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $remessa->devolvido_por }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $remessa->itens_count }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('descarte.remessas.show', $remessa) }}" target="_blank" class="text-brand-600 hover:underline dark:text-brand-400">{{ __('Ver / Imprimir') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400" colspan="4">{{ __('Nenhuma remessa gerada ainda.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
