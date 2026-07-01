<div>
    <x-input-label for="nome" :value="__('Nome da máquina')" />
    <x-text-input id="nome" name="nome" type="text" class="block mt-1 w-full"
                  :value="old('nome', $maquina->nome ?? '')" required autofocus />
    <x-input-error :messages="$errors->get('nome')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="setor_id" :value="__('Setor')" />
    <select id="setor_id" name="setor_id" class="block mt-1 w-full rounded-md border-gray-300" required>
        <option value="">{{ __('Selecione um setor') }}</option>
        @foreach ($setores as $setor)
            <option value="{{ $setor->id }}" @selected(old('setor_id', $maquina->setor_id ?? '') == $setor->id)>
                {{ $setor->nome }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('setor_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="sistema_operacional" :value="__('Sistema operacional')" />
    <x-text-input id="sistema_operacional" name="sistema_operacional" type="text" class="block mt-1 w-full"
                  :value="old('sistema_operacional', $maquina->sistema_operacional ?? '')"
                  placeholder="Ex: Windows 11" />
    <x-input-error :messages="$errors->get('sistema_operacional')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="processador" :value="__('Processador')" />
    <x-text-input id="processador" name="processador" type="text" class="block mt-1 w-full"
                  :value="old('processador', $maquina->processador ?? '')" required
                  placeholder="Ex: Intel Core i5-10400" />
    <x-input-error :messages="$errors->get('processador')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="memoria_ram_gb" :value="__('Memória RAM (GB)')" />
    <x-text-input id="memoria_ram_gb" name="memoria_ram_gb" type="number" min="1" class="block mt-1 w-full"
                  :value="old('memoria_ram_gb', $maquina->memoria_ram_gb ?? '')" />
    <x-input-error :messages="$errors->get('memoria_ram_gb')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="tipo_armazenamento" :value="__('Tipo de armazenamento')" />
    <select id="tipo_armazenamento" name="tipo_armazenamento" class="block mt-1 w-full rounded-md border-gray-300" required>
        @foreach (['HD', 'SSD'] as $tipo)
            <option value="{{ $tipo }}" @selected(old('tipo_armazenamento', $maquina->tipo_armazenamento ?? '') == $tipo)>
                {{ $tipo }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('tipo_armazenamento')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="capacidade_armazenamento_gb" :value="__('Capacidade de armazenamento (GB)')" />
    <x-text-input id="capacidade_armazenamento_gb" name="capacidade_armazenamento_gb" type="number" min="1" class="block mt-1 w-full"
                  :value="old('capacidade_armazenamento_gb', $maquina->capacidade_armazenamento_gb ?? '')" required />
    <x-input-error :messages="$errors->get('capacidade_armazenamento_gb')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="observacoes" :value="__('Observações')" />
    <textarea id="observacoes" name="observacoes" rows="3" class="block mt-1 w-full rounded-md border-gray-300"
              placeholder="Ex: GPU, IP, notas gerais">{{ old('observacoes', $maquina->observacoes ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('observacoes')" class="mt-2" />
</div>

<div class="flex items-center gap-4 mt-6">
    <x-primary-button>{{ __('Salvar') }}</x-primary-button>
    <a href="{{ route('maquinas.index') }}" class="text-sm text-gray-600 hover:underline">{{ __('Cancelar') }}</a>
</div>
