<div>
    <x-input-label for="nome" :value="__('Nome')" />
    <x-text-input id="nome" name="nome" type="text" class="block mt-1 w-full"
                  :value="old('nome', $setor->nome ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('nome')" class="mt-2" />
</div>

<div class="flex items-center gap-4 mt-6">
    <x-primary-button>{{ __('Salvar') }}</x-primary-button>
    <a href="{{ route('setores.index') }}" class="text-sm text-gray-600 hover:underline">{{ __('Cancelar') }}</a>
</div>
