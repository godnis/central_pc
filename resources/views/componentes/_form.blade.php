@php
    $specs = $componente->specs ?? [];
    $tiposRam = \App\Support\ComponenteOpcoes::tiposRam();
    $interfacesArmazenamento = \App\Support\ComponenteOpcoes::interfacesArmazenamento();
    $tiposArmazenamento = \App\Support\ComponenteOpcoes::tiposArmazenamento();
    $formFactors = \App\Support\ComponenteOpcoes::formFactors();
@endphp

<div x-data="{ categoria: '{{ old('categoria', $componente->categoria->value ?? '') }}' }">
    <div>
        <x-input-label for="categoria" :value="__('Categoria')" />
        <select id="categoria" name="categoria" x-model="categoria" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500" required @if($componente->exists) disabled @endif>
            <option value="">{{ __('Selecione uma categoria') }}</option>
            @foreach ($categorias as $cat)
                <option value="{{ $cat->value }}" @selected(old('categoria', $componente->categoria->value ?? '') === $cat->value)>
                    {{ $cat->label() }}
                </option>
            @endforeach
        </select>
        @if ($componente->exists)
            {{-- categoria não muda depois de criado: specs têm formato diferente por categoria --}}
            <input type="hidden" name="categoria" value="{{ $componente->categoria->value }}">
        @endif
        <x-input-error :messages="$errors->get('categoria')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="nome" :value="__('Nome')" />
        <x-text-input id="nome" name="nome" type="text" class="block mt-1 w-full"
                      :value="old('nome', $componente->nome ?? '')" required autofocus
                      placeholder="Ex: Intel Core i5-10400" />
        <x-input-error :messages="$errors->get('nome')" class="mt-2" />
    </div>

    <div class="mt-4">
        <x-input-label for="fabricante" :value="__('Fabricante')" />
        <x-text-input id="fabricante" name="fabricante" type="text" class="block mt-1 w-full"
                      :value="old('fabricante', $componente->fabricante ?? '')" placeholder="Ex: Intel" />
        <x-input-error :messages="$errors->get('fabricante')" class="mt-2" />
    </div>

    <div class="mt-4">
        <label class="inline-flex items-center">
            <input type="hidden" name="ativo" value="0">
            <input type="checkbox" name="ativo" value="1" class="rounded border-gray-300"
                   @checked(old('ativo', $componente->ativo ?? true))>
            <span class="ms-2 text-sm text-gray-700">{{ __('Ativo (disponível para seleção no cadastro de máquinas)') }}</span>
        </label>
    </div>

    <fieldset class="mt-8 border-t border-gray-200 pt-6" x-show="categoria === 'cpu'">
        <legend class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-600 mb-3">{{ __('Specs de processador') }}</legend>
        <div>
            <x-input-label for="specs_socket_cpu" :value="__('Socket')" />
            <x-text-input id="specs_socket_cpu" name="specs[socket]" type="text" class="block mt-1 w-full"
                          :value="old('specs.socket', $specs['socket'] ?? '')" placeholder="Ex: LGA1200" />
        </div>
        <div class="mt-4">
            <x-input-label for="specs_tdp_watts" :value="__('TDP (watts)')" />
            <x-text-input id="specs_tdp_watts" name="specs[tdp_watts]" type="number" min="0" class="block mt-1 w-full"
                          :value="old('specs.tdp_watts', $specs['tdp_watts'] ?? '')" />
        </div>
    </fieldset>

    <fieldset class="mt-8 border-t border-gray-200 pt-6" x-show="categoria === 'placa_mae'">
        <legend class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-600 mb-3">{{ __('Specs de placa-mãe') }}</legend>
        <div>
            <x-input-label for="specs_socket_placa" :value="__('Socket')" />
            <x-text-input id="specs_socket_placa" name="specs[socket]" type="text" class="block mt-1 w-full"
                          :value="old('specs.socket', $specs['socket'] ?? '')" placeholder="Ex: LGA1200" />
        </div>
        <div class="mt-4">
            <x-input-label for="specs_form_factor" :value="__('Form factor')" />
            <input list="opcoes-form-factor" id="specs_form_factor" name="specs[form_factor]" type="text"
                   class="block mt-1 w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500 shadow-sm"
                   value="{{ old('specs.form_factor', $specs['form_factor'] ?? '') }}">
            <datalist id="opcoes-form-factor">
                @foreach ($formFactors as $opcao)
                    <option value="{{ $opcao }}">
                @endforeach
            </datalist>
        </div>
        <div class="mt-4">
            <x-input-label :value="__('Tipos de RAM suportados')" />
            <div class="mt-1 flex flex-wrap gap-4">
                @foreach ($tiposRam as $opcao)
                    <label class="inline-flex items-center text-sm text-gray-700">
                        <input type="checkbox" name="specs[tipos_ram_suportados][]" value="{{ $opcao }}" class="rounded border-gray-300"
                               @checked(in_array($opcao, old('specs.tipos_ram_suportados', $specs['tipos_ram_suportados'] ?? [])))>
                        <span class="ms-1">{{ $opcao }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="mt-4">
            <x-input-label :value="__('Interfaces de armazenamento suportadas')" />
            <div class="mt-1 flex flex-wrap gap-4">
                @foreach ($interfacesArmazenamento as $opcao)
                    <label class="inline-flex items-center text-sm text-gray-700">
                        <input type="checkbox" name="specs[interfaces_armazenamento_suportadas][]" value="{{ $opcao }}" class="rounded border-gray-300"
                               @checked(in_array($opcao, old('specs.interfaces_armazenamento_suportadas', $specs['interfaces_armazenamento_suportadas'] ?? [])))>
                        <span class="ms-1">{{ $opcao }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="mt-4">
            <x-input-label for="specs_slots_ram" :value="__('Slots de RAM')" />
            <x-text-input id="specs_slots_ram" name="specs[slots_ram]" type="number" min="0" class="block mt-1 w-full"
                          :value="old('specs.slots_ram', $specs['slots_ram'] ?? '')" />
        </div>
    </fieldset>

    <fieldset class="mt-8 border-t border-gray-200 pt-6" x-show="categoria === 'ram'">
        <legend class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-600 mb-3">{{ __('Specs de memória RAM') }}</legend>
        <div>
            <x-input-label for="specs_tipo_ram" :value="__('Tipo')" />
            <select id="specs_tipo_ram" name="specs[tipo]" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
                <option value="">{{ __('Selecione') }}</option>
                @foreach ($tiposRam as $opcao)
                    <option value="{{ $opcao }}" @selected(old('specs.tipo', $specs['tipo'] ?? '') === $opcao)>{{ $opcao }}</option>
                @endforeach
            </select>
        </div>
        <div class="mt-4">
            <x-input-label for="specs_capacidade_gb_ram" :value="__('Capacidade (GB)')" />
            <x-text-input id="specs_capacidade_gb_ram" name="specs[capacidade_gb]" type="number" min="0" class="block mt-1 w-full"
                          :value="old('specs.capacidade_gb', $specs['capacidade_gb'] ?? '')" />
        </div>
        <div class="mt-4">
            <x-input-label for="specs_velocidade_mhz" :value="__('Velocidade (MHz)')" />
            <x-text-input id="specs_velocidade_mhz" name="specs[velocidade_mhz]" type="number" min="0" class="block mt-1 w-full"
                          :value="old('specs.velocidade_mhz', $specs['velocidade_mhz'] ?? '')" />
        </div>
    </fieldset>

    <fieldset class="mt-8 border-t border-gray-200 pt-6" x-show="categoria === 'armazenamento'">
        <legend class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-600 mb-3">{{ __('Specs de armazenamento') }}</legend>
        <div>
            <x-input-label for="specs_tipo_armazenamento" :value="__('Tipo')" />
            <select id="specs_tipo_armazenamento" name="specs[tipo]" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
                <option value="">{{ __('Selecione') }}</option>
                @foreach ($tiposArmazenamento as $opcao)
                    <option value="{{ $opcao }}" @selected(old('specs.tipo', $specs['tipo'] ?? '') === $opcao)>{{ $opcao }}</option>
                @endforeach
            </select>
        </div>
        <div class="mt-4">
            <x-input-label for="specs_interface" :value="__('Interface')" />
            <select id="specs_interface" name="specs[interface]" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-brand-500 focus:ring-brand-500">
                <option value="">{{ __('Selecione') }}</option>
                @foreach ($interfacesArmazenamento as $opcao)
                    <option value="{{ $opcao }}" @selected(old('specs.interface', $specs['interface'] ?? '') === $opcao)>{{ $opcao }}</option>
                @endforeach
            </select>
        </div>
        <div class="mt-4">
            <x-input-label for="specs_capacidade_gb_armazenamento" :value="__('Capacidade (GB)')" />
            <x-text-input id="specs_capacidade_gb_armazenamento" name="specs[capacidade_gb]" type="number" min="0" class="block mt-1 w-full"
                          :value="old('specs.capacidade_gb', $specs['capacidade_gb'] ?? '')" />
        </div>
    </fieldset>

    <fieldset class="mt-8 border-t border-gray-200 pt-6" x-show="categoria === 'gpu'">
        <legend class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-600 mb-3">{{ __('Specs de placa de vídeo') }}</legend>
        <div>
            <x-input-label for="specs_consumo_watts" :value="__('Consumo (watts)')" />
            <x-text-input id="specs_consumo_watts" name="specs[consumo_watts]" type="number" min="0" class="block mt-1 w-full"
                          :value="old('specs.consumo_watts', $specs['consumo_watts'] ?? '')" />
        </div>
    </fieldset>

    <fieldset class="mt-8 border-t border-gray-200 pt-6" x-show="categoria === 'fonte'">
        <legend class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-600 mb-3">{{ __('Specs de fonte') }}</legend>
        <div>
            <x-input-label for="specs_potencia_watts" :value="__('Potência (watts)')" />
            <x-text-input id="specs_potencia_watts" name="specs[potencia_watts]" type="number" min="0" class="block mt-1 w-full"
                          :value="old('specs.potencia_watts', $specs['potencia_watts'] ?? '')" />
        </div>
    </fieldset>

    <fieldset class="mt-8 border-t border-gray-200 pt-6" x-show="categoria === 'gabinete'">
        <legend class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-600 mb-3">{{ __('Specs de gabinete') }}</legend>
        <div>
            <x-input-label :value="__('Form factors suportados')" />
            <div class="mt-1 flex flex-wrap gap-4">
                @foreach ($formFactors as $opcao)
                    <label class="inline-flex items-center text-sm text-gray-700">
                        <input type="checkbox" name="specs[form_factors_suportados][]" value="{{ $opcao }}" class="rounded border-gray-300"
                               @checked(in_array($opcao, old('specs.form_factors_suportados', $specs['form_factors_suportados'] ?? [])))>
                        <span class="ms-1">{{ $opcao }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </fieldset>

    <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-200 pt-6">
        <a href="{{ route('componentes.index') }}" class="inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('Cancelar') }}</a>
        <x-primary-button>{{ __('Salvar componente') }}</x-primary-button>
    </div>
</div>
