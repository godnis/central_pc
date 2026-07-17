<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-lg tracking-tight text-gray-800 dark:text-gray-100 leading-tight">{{ __('Tokens de API') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-green-100 text-green-800 rounded-md">{{ session('status') }}</div>
            @endif

            @if ($novoToken)
                <div class="p-4 bg-amber-100 text-amber-800 rounded-md text-sm">
                    <p class="font-semibold mb-1">{{ __('Guarde este token agora — ele não será mostrado de novo:') }}</p>
                    <code class="block bg-white p-2 rounded border break-all">{{ $novoToken }}</code>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ __('Novo token') }}</h3>
                <form action="{{ route('tokens.store') }}" method="POST" class="flex items-end gap-3">
                    @csrf
                    <div class="flex-1">
                        <x-input-label for="nome" :value="__('Nome do token (ex: integração X)')" />
                        <x-text-input id="nome" name="nome" type="text" class="block mt-1 w-full" required />
                        <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                    </div>
                    <x-primary-button>{{ __('Gerar') }}</x-primary-button>
                </form>
            </div>

            <div class="bg-white overflow-x-auto shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Nome') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Criado em') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Último uso') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($tokens as $token)
                            <tr>
                                <td class="px-6 py-4">{{ $token->name }}</td>
                                <td class="px-6 py-4">{{ $token->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4">{{ optional($token->last_used_at)->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('tokens.destroy', $token->id) }}" method="POST"
                                          onsubmit="return confirm('{{ __('Revogar este token?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">{{ __('Revogar') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 text-gray-500" colspan="4">{{ __('Nenhum token gerado ainda.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
