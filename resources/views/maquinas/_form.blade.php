@php
    // old() é uma flash de sessão global — se essa falhar a validação de um
    // formulário diferente (ex: criar outra máquina) e o usuário navegar
    // direto pra cá antes de reenviar, old() ainda estaria "quente" e
    // mostraria os dados errados. O campo oculto _form_maquina_id só deixa
    // reaproveitar old() quando a página redisplayed é literalmente esta.
    $idAtual = isset($maquina) && $maquina->exists ? (string) $maquina->id : 'novo';
    $formularioEhOMesmoRegistro = old('_form_maquina_id') === $idAtual;
    $old = fn (string $campo, $default = null) => $formularioEhOMesmoRegistro ? old($campo, $default) : $default;

    $selecionadosIniciais = $old('componentes', $selecionadosAtuais ?? []) + [
        'cpu' => null,
        'placa_mae' => null,
        'ram' => [],
        'armazenamento' => [],
        'gpu' => null,
        'fonte' => null,
        'gabinete' => null,
    ];
@endphp

<input type="hidden" name="_form_maquina_id" value="{{ $idAtual }}">

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="nome" :value="__('Nome da máquina')" />
        <x-text-input id="nome" name="nome" type="text" class="block mt-1 w-full"
                      :value="$old('nome', $maquina->nome ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('nome')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="patrimonio" :value="__('Patrimônio (opcional)')" />
        <x-text-input id="patrimonio" name="patrimonio" type="text" class="block mt-1 w-full"
                      :value="$old('patrimonio', $maquina->patrimonio ?? '')" placeholder="Ex: PAT-00123" />
        <x-input-error :messages="$errors->get('patrimonio')" class="mt-2" />
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
    <div>
        <x-input-label for="setor_id" :value="__('Setor')" />
        <select id="setor_id" name="setor_id" class="block mt-1 w-full rounded-md border-gray-300" required>
            <option value="">{{ __('Selecione um setor') }}</option>
            @foreach ($setores as $setor)
                <option value="{{ $setor->id }}" @selected($old('setor_id', $maquina->setor_id ?? '') == $setor->id)>
                    {{ $setor->nome }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('setor_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300" required>
            @foreach ($statusList as $statusOpcao)
                <option value="{{ $statusOpcao->value }}" @selected($old('status', $maquina->status?->value ?? 'ativa') === $statusOpcao->value)>
                    {{ $statusOpcao->label() }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
    <div>
        <x-input-label for="sistema_operacional" :value="__('Sistema operacional')" />
        <x-text-input id="sistema_operacional" name="sistema_operacional" type="text" class="block mt-1 w-full"
                      :value="$old('sistema_operacional', $maquina->sistema_operacional ?? '')"
                      placeholder="Ex: Windows 11" />
        <x-input-error :messages="$errors->get('sistema_operacional')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="responsavel" :value="__('Responsável (opcional)')" />
        <x-text-input id="responsavel" name="responsavel" type="text" class="block mt-1 w-full"
                      :value="$old('responsavel', $maquina->responsavel ?? '')" placeholder="Ex: nome do colaborador" />
        <x-input-error :messages="$errors->get('responsavel')" class="mt-2" />
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
    <div>
        <x-input-label for="data_aquisicao" :value="__('Data de aquisição (opcional)')" />
        <x-text-input id="data_aquisicao" name="data_aquisicao" type="date" class="block mt-1 w-full"
                      :value="$old('data_aquisicao', optional($maquina->data_aquisicao ?? null)->format('Y-m-d'))" />
        <x-input-error :messages="$errors->get('data_aquisicao')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="foto" :value="__('Foto da máquina (opcional)')" />
        <input id="foto" name="foto" type="file" accept="image/*" class="block mt-1 w-full text-sm">
        @if (($maquina->foto_path ?? null))
            <img src="{{ Storage::url($maquina->foto_path) }}" alt="" class="mt-2 h-20 w-20 object-cover rounded-md border">
        @endif
        <x-input-error :messages="$errors->get('foto')" class="mt-2" />
    </div>
</div>

<div class="mt-6 border-t pt-4"
     x-data="maquinaComponentesForm({
         componentesIniciais: @js($componentesPorCategoria),
         selecionados: @js($selecionadosIniciais),
         compativeisUrl: '{{ route('componentes.compativeis') }}',
     })">
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-700">{{ __('Componentes') }}</h3>
        <a href="{{ route('componentes.create') }}" target="_blank" class="text-xs text-indigo-600 hover:underline">
            {{ __('+ cadastrar componente no catálogo') }}
        </a>
    </div>

    <template x-if="carregando">
        <p class="text-xs text-gray-400 mt-1">{{ __('Atualizando opções…') }}</p>
    </template>

    {{-- CPU --}}
    <div class="mt-3">
        <x-input-label for="componente_cpu" :value="__('Processador')" />
        <select id="componente_cpu" x-model.number="selecionados.cpu" @change="onCpuChange()"
                name="componentes[cpu]" class="block mt-1 w-full rounded-md border-gray-300" required>
            <option value="">{{ __('Selecione') }}</option>
            <template x-for="item in opcoes.cpu" :key="item.id">
                <option :value="item.id" x-text="item.nome + (item.fabricante ? ' — ' + item.fabricante : '')"></option>
            </template>
        </select>
        <x-input-error :messages="$errors->get('componentes.cpu')" class="mt-2" />
    </div>

    {{-- Placa-mãe --}}
    <div class="mt-4">
        <x-input-label for="componente_placa_mae" :value="__('Placa-mãe')" />
        <select id="componente_placa_mae" x-model.number="selecionados.placa_mae" @change="onPlacaMaeChange()"
                name="componentes[placa_mae]" class="block mt-1 w-full rounded-md border-gray-300" required>
            <option value="">{{ __('Selecione') }}</option>
            <template x-for="item in opcoes.placa_mae" :key="item.id">
                <option :value="item.id" x-text="item.nome + (item.fabricante ? ' — ' + item.fabricante : '')"></option>
            </template>
        </select>
        <p class="text-xs text-gray-400 mt-1">{{ __('Escolher o processador primeiro filtra as placas-mãe compatíveis.') }}</p>
        <x-input-error :messages="$errors->get('componentes.placa_mae')" class="mt-2" />
    </div>

    {{-- RAM (múltiplo) --}}
    <div class="mt-4">
        <x-input-label :value="__('Memória RAM')" />
        <template x-for="(item, index) in selecionados.ram" :key="index">
            <div class="flex items-center gap-2 mt-2">
                <select x-model.number="item.componente_id" :name="`componentes[ram][${index}][componente_id]`"
                        class="flex-1 rounded-md border-gray-300 text-sm" required>
                    <option value="">{{ __('Selecione') }}</option>
                    <template x-for="opcao in opcoes.ram" :key="opcao.id">
                        <option :value="opcao.id" x-text="opcao.nome"></option>
                    </template>
                </select>
                <input type="number" min="1" x-model.number="item.quantidade"
                       :name="`componentes[ram][${index}][quantidade]`"
                       class="w-20 rounded-md border-gray-300 text-sm" required>
                <button type="button" @click="removerRam(index)" class="text-red-600 text-sm">{{ __('Remover') }}</button>
            </div>
        </template>
        <button type="button" @click="adicionarRam()" class="mt-2 text-sm text-indigo-600 hover:underline">
            {{ __('+ Adicionar RAM') }}
        </button>
        <x-input-error :messages="$errors->get('componentes.ram')" class="mt-2" />
    </div>

    {{-- Armazenamento (múltiplo) --}}
    <div class="mt-4">
        <x-input-label :value="__('Armazenamento')" />
        <template x-for="(item, index) in selecionados.armazenamento" :key="index">
            <div class="flex items-center gap-2 mt-2">
                <select x-model.number="item.componente_id" :name="`componentes[armazenamento][${index}][componente_id]`"
                        class="flex-1 rounded-md border-gray-300 text-sm" required>
                    <option value="">{{ __('Selecione') }}</option>
                    <template x-for="opcao in opcoes.armazenamento" :key="opcao.id">
                        <option :value="opcao.id" x-text="opcao.nome"></option>
                    </template>
                </select>
                <input type="number" min="1" x-model.number="item.quantidade"
                       :name="`componentes[armazenamento][${index}][quantidade]`"
                       class="w-20 rounded-md border-gray-300 text-sm" required>
                <button type="button" @click="removerArmazenamento(index)" class="text-red-600 text-sm">{{ __('Remover') }}</button>
            </div>
        </template>
        <button type="button" @click="adicionarArmazenamento()" class="mt-2 text-sm text-indigo-600 hover:underline">
            {{ __('+ Adicionar armazenamento') }}
        </button>
        <x-input-error :messages="$errors->get('componentes.armazenamento')" class="mt-2" />
    </div>

    {{-- GPU (opcional) --}}
    <div class="mt-4">
        <x-input-label for="componente_gpu" :value="__('Placa de vídeo (opcional)')" />
        <select id="componente_gpu" x-model.number="selecionados.gpu" @change="onGpuChange()"
                name="componentes[gpu]" class="block mt-1 w-full rounded-md border-gray-300">
            <option value="">{{ __('Nenhuma / integrada') }}</option>
            <template x-for="item in opcoes.gpu" :key="item.id">
                <option :value="item.id" x-text="item.nome + (item.fabricante ? ' — ' + item.fabricante : '')"></option>
            </template>
        </select>
        <x-input-error :messages="$errors->get('componentes.gpu')" class="mt-2" />
    </div>

    {{-- Fonte (opcional) --}}
    <div class="mt-4">
        <x-input-label for="componente_fonte" :value="__('Fonte (opcional)')" />
        <select id="componente_fonte" x-model.number="selecionados.fonte" @change="onFonteChange()"
                name="componentes[fonte]" class="block mt-1 w-full rounded-md border-gray-300">
            <option value="">{{ __('Não informado') }}</option>
            <template x-for="item in opcoes.fonte" :key="item.id">
                <option :value="item.id" x-text="item.nome + (item.fabricante ? ' — ' + item.fabricante : '')"></option>
            </template>
        </select>
        <template x-if="avisoFonte">
            <div class="mt-2 p-3 bg-amber-100 text-amber-800 rounded-md text-sm" x-text="avisoFonte"></div>
        </template>
        <x-input-error :messages="$errors->get('componentes.fonte')" class="mt-2" />
    </div>

    {{-- Gabinete (opcional) --}}
    <div class="mt-4">
        <x-input-label for="componente_gabinete" :value="__('Gabinete (opcional)')" />
        <select id="componente_gabinete" x-model.number="selecionados.gabinete"
                name="componentes[gabinete]" class="block mt-1 w-full rounded-md border-gray-300">
            <option value="">{{ __('Não informado') }}</option>
            <template x-for="item in opcoes.gabinete" :key="item.id">
                <option :value="item.id" x-text="item.nome + (item.fabricante ? ' — ' + item.fabricante : '')"></option>
            </template>
        </select>
        <x-input-error :messages="$errors->get('componentes.gabinete')" class="mt-2" />
    </div>
</div>

<div class="mt-6">
    <x-input-label for="observacoes" :value="__('Observações')" />
    <textarea id="observacoes" name="observacoes" rows="3" class="block mt-1 w-full rounded-md border-gray-300"
              placeholder="Ex: IP, notas gerais">{{ $old('observacoes', $maquina->observacoes ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('observacoes')" class="mt-2" />
</div>

<div class="flex items-center gap-4 mt-6">
    <x-primary-button>{{ __('Salvar') }}</x-primary-button>
    <a href="{{ route('maquinas.index') }}" class="text-sm text-gray-600 hover:underline">{{ __('Cancelar') }}</a>
</div>
