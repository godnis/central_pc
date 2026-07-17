<x-mail::message>
# Relatório de manutenção preventiva

@if ($maquinasAntigas->isNotEmpty())
## Máquinas com mais de 3 anos de uso

@foreach ($maquinasAntigas as $maquina)
- **{{ $maquina->nome }}** ({{ $maquina->setor->nome }}) — adquirida em {{ $maquina->data_aquisicao->format('d/m/Y') }}
@endforeach
@endif

@if ($maquinasEmManutencao->isNotEmpty())
## Máquinas em manutenção há mais de 30 dias

@foreach ($maquinasEmManutencao as $maquina)
- **{{ $maquina->nome }}** ({{ $maquina->setor->nome }}) — desde {{ $maquina->updated_at->format('d/m/Y') }}
@endforeach
@endif

<x-mail::button :url="route('maquinas.index')">
Ver máquinas
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
