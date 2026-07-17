<?php

namespace App\Http\Controllers\Concerns;

use App\Enums\CategoriaComponente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Validação dos dados de um componente do catálogo, incluindo os campos
 * dinâmicos de `specs` conforme a categoria — compartilhada entre o
 * controller web e o da API.
 */
trait ValidaDadosDeComponente
{
    protected function validarDadosComponente(Request $request): array
    {
        $dados = $request->validate([
            'categoria' => ['required', Rule::enum(CategoriaComponente::class)],
            'nome' => 'required|string|max:255',
            'fabricante' => 'nullable|string|max:255',
        ]);

        $categoria = CategoriaComponente::from($dados['categoria']);

        $dados['ativo'] = $request->boolean('ativo', true);
        $dados['specs'] = $this->extrairSpecs($request, $categoria);

        return $dados;
    }

    protected function extrairSpecs(Request $request, CategoriaComponente $categoria): array
    {
        $specs = [];

        foreach ($categoria->camposDeSpecs() as $campo => $tipo) {
            $valor = $request->input("specs.{$campo}");

            $specs[$campo] = match ($tipo) {
                'array' => array_values(array_filter((array) $valor, fn ($item) => filled($item))),
                'integer' => filled($valor) ? (int) $valor : null,
                default => filled($valor) ? (string) $valor : null,
            };
        }

        return $specs;
    }
}
