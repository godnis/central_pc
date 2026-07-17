<div>
    <x-input-label for="nome" :value="__('Nome')" />
    <x-text-input id="nome" name="nome" type="text" class="block mt-1 w-full"
                  :value="old('nome', $setor->nome ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('nome')" class="mt-2" />
</div>

<div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-200 pt-6">
    <a href="{{ route('setores.index') }}" class="inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('Cancelar') }}</a>
    <x-primary-button>{{ __('Salvar setor') }}</x-primary-button>
</div>
