<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-lg tracking-tight text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Nova peça para descarte') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Registre a peça que não funcionou. Ela entra na fila de descarte até você gerar o Anexo 7 de devolução ao almoxarifado.') }}
            </p>

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                    <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">{{ __('Identificação da peça') }}</p>
                </div>

                <form action="{{ route('descarte.store') }}" method="POST" class="px-6 py-6">
                    @csrf

                    <div>
                        <x-input-label for="descricao" :value="__('Descrição do material')" />
                        <x-text-input id="descricao" name="descricao" type="text" class="block mt-1 w-full"
                                      :value="old('descricao')" required autofocus
                                      placeholder="Ex: Memória RAM DDR3 4GB Kingston" />
                        <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="diagnostico" :value="__('Diagnóstico')" />
                        <textarea id="diagnostico" name="diagnostico" rows="3"
                                  class="block mt-1 w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500 dark:bg-gray-900 dark:border-gray-600 dark:text-gray-100"
                                  placeholder="Ex: Não liga / não é reconhecida pela placa-mãe"
                                  required>{{ old('diagnostico') }}</textarea>
                        <x-input-error :messages="$errors->get('diagnostico')" class="mt-2" />
                    </div>

                    <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                        <a href="{{ route('descarte.index') }}" class="inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">{{ __('Cancelar') }}</a>
                        <x-primary-button>{{ __('Adicionar à fila') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
