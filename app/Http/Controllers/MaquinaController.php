<?php

namespace App\Http\Controllers;

use App\Enums\CategoriaComponente;
use App\Models\Componente;
use App\Models\Maquina;
use App\Models\Setor;
use App\Services\CompatibilidadeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MaquinaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $setorId = $request->query('setor_id');

        $maquinas = Maquina::with(['setor', 'maquinaComponentes.componente'])
            ->when($setorId, fn ($query) => $query->where('setor_id', $setorId))
            ->orderBy('nome')
            ->get();

        $totalGeral = Maquina::count();

        $totalPorSetor = Setor::withCount('maquinas')
            ->get()
            ->filter(fn ($setor) => $setor->maquinas_count > 0)
            ->sortByDesc('maquinas_count');

        $setores = Setor::orderBy('nome')->get();

        return view('maquinas.index', compact('maquinas', 'totalGeral', 'totalPorSetor', 'setores', 'setorId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $setores = Setor::orderBy('nome')->get();
        $componentesPorCategoria = $this->componentesAtivosPorCategoria();

        return view('maquinas.create', compact('setores', 'componentesPorCategoria'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $dados = $this->validarDados($request);
        $this->validarCompatibilidade($dados['componentes']);

        $maquina = Maquina::create($this->dadosDaMaquina($dados));
        $this->sincronizarComponentes($maquina, $dados['componentes']);

        return redirect()->route('maquinas.index')->with('status', 'Máquina cadastrada com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Maquina $maquina): View
    {
        $setores = Setor::orderBy('nome')->get();
        $componentesPorCategoria = $this->componentesAtivosPorCategoria();

        $maquina->load('maquinaComponentes.componente');
        $selecionadosAtuais = $this->selecionadosAtuais($maquina);

        return view('maquinas.edit', compact(
            'maquina', 'setores', 'componentesPorCategoria', 'selecionadosAtuais'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Maquina $maquina): RedirectResponse
    {
        $dados = $this->validarDados($request);
        $this->validarCompatibilidade($dados['componentes']);

        $maquina->update($this->dadosDaMaquina($dados));
        $this->sincronizarComponentes($maquina, $dados['componentes']);

        return redirect()->route('maquinas.index')->with('status', 'Máquina atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Maquina $maquina): RedirectResponse
    {
        $maquina->delete();

        return redirect()->route('maquinas.index')->with('status', 'Máquina excluída com sucesso.');
    }

    /**
     * Valida os dados do formulário de máquina, incluindo os componentes
     * selecionados do catálogo.
     */
    private function validarDados(Request $request): array
    {
        return $request->validate([
            'nome' => 'required|string|max:255',
            'setor_id' => 'required|exists:setores,id',
            'sistema_operacional' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
            'componentes.cpu' => 'required|integer|exists:componentes,id',
            'componentes.placa_mae' => 'required|integer|exists:componentes,id',
            'componentes.ram' => 'required|array|min:1',
            'componentes.ram.*.componente_id' => 'required|integer|exists:componentes,id',
            'componentes.ram.*.quantidade' => 'required|integer|min:1',
            'componentes.armazenamento' => 'required|array|min:1',
            'componentes.armazenamento.*.componente_id' => 'required|integer|exists:componentes,id',
            'componentes.armazenamento.*.quantidade' => 'required|integer|min:1',
            'componentes.gpu' => 'nullable|integer|exists:componentes,id',
            'componentes.fonte' => 'nullable|integer|exists:componentes,id',
            'componentes.gabinete' => 'nullable|integer|exists:componentes,id',
        ]);
    }

    private function dadosDaMaquina(array $dados): array
    {
        return [
            'nome' => $dados['nome'],
            'setor_id' => $dados['setor_id'],
            'sistema_operacional' => $dados['sistema_operacional'] ?? null,
            'observacoes' => $dados['observacoes'] ?? null,
        ];
    }

    /**
     * Reaplica as regras de compatibilidade no backend (o formulário já
     * restringe as opções via AJAX, mas isso não deve ser a única defesa).
     */
    private function validarCompatibilidade(array $componentes): void
    {
        $ids = collect([
            $componentes['cpu'] ?? null,
            $componentes['placa_mae'] ?? null,
            $componentes['gpu'] ?? null,
            $componentes['fonte'] ?? null,
            $componentes['gabinete'] ?? null,
        ])
            ->merge(collect($componentes['ram'] ?? [])->pluck('componente_id'))
            ->merge(collect($componentes['armazenamento'] ?? [])->pluck('componente_id'))
            ->filter()
            ->unique();

        $porId = Componente::query()->whereIn('id', $ids)->get()->keyBy('id');
        $buscar = fn (?int $id) => $id ? $porId->get($id) : null;

        $selecionados = [
            CategoriaComponente::Cpu->value => $buscar($componentes['cpu'] ?? null),
            CategoriaComponente::PlacaMae->value => $buscar($componentes['placa_mae'] ?? null),
            CategoriaComponente::Ram->value => collect($componentes['ram'] ?? [])
                ->map(fn ($item) => $buscar($item['componente_id']))->filter()->values(),
            CategoriaComponente::Armazenamento->value => collect($componentes['armazenamento'] ?? [])
                ->map(fn ($item) => $buscar($item['componente_id']))->filter()->values(),
            CategoriaComponente::Gpu->value => $buscar($componentes['gpu'] ?? null),
            CategoriaComponente::Fonte->value => $buscar($componentes['fonte'] ?? null),
            CategoriaComponente::Gabinete->value => $buscar($componentes['gabinete'] ?? null),
        ];

        $service = app(CompatibilidadeService::class);
        $erros = [];

        foreach ([CategoriaComponente::Cpu, CategoriaComponente::PlacaMae, CategoriaComponente::Ram, CategoriaComponente::Armazenamento, CategoriaComponente::Gabinete] as $categoria) {
            $valor = $selecionados[$categoria->value];
            $itens = $valor instanceof Collection ? $valor : collect($valor ? [$valor] : []);

            if ($itens->isEmpty()) {
                continue;
            }

            // Array puro (sem passar por Collection::toArray(), que chamaria
            // Componente::toArray() e transformaria os models em arrays).
            $outrasSelecoes = $selecionados;
            unset($outrasSelecoes[$categoria->value]);
            $idsCompativeis = $service->componentesCompativeis($outrasSelecoes, $categoria)->pluck('id');

            foreach ($itens as $item) {
                if (! $idsCompativeis->contains($item->id)) {
                    $erros["componentes.{$categoria->value}"] = 'O componente selecionado para '
                        .$categoria->label().' não é compatível com os demais componentes escolhidos.';
                    break;
                }
            }
        }

        if ($erros) {
            throw ValidationException::withMessages($erros);
        }
    }

    private function sincronizarComponentes(Maquina $maquina, array $componentes): void
    {
        $maquina->maquinaComponentes()->delete();

        $linhas = [];

        foreach ([CategoriaComponente::Cpu, CategoriaComponente::PlacaMae, CategoriaComponente::Gpu, CategoriaComponente::Fonte, CategoriaComponente::Gabinete] as $categoria) {
            $id = $componentes[$categoria->value] ?? null;
            if ($id) {
                $linhas[] = ['componente_id' => $id, 'quantidade' => 1];
            }
        }

        foreach ([CategoriaComponente::Ram, CategoriaComponente::Armazenamento] as $categoria) {
            foreach ($componentes[$categoria->value] ?? [] as $item) {
                $linhas[] = ['componente_id' => $item['componente_id'], 'quantidade' => $item['quantidade']];
            }
        }

        foreach ($linhas as $linha) {
            $maquina->maquinaComponentes()->create($linha);
        }
    }

    /**
     * Mesmo formato do endpoint /componentes/compativeis, para popular os
     * selects do formulário antes de qualquer filtragem via AJAX.
     *
     * @return Collection<string, Collection<int, array{id: int, nome: string, fabricante: ?string}>>
     */
    private function componentesAtivosPorCategoria(): Collection
    {
        return Componente::query()
            ->ativos()
            ->orderBy('nome')
            ->get()
            ->groupBy(fn (Componente $componente) => $componente->categoria->value)
            ->map(fn (Collection $itens) => $itens->map(fn (Componente $c) => [
                'id' => $c->id,
                'nome' => $c->nome,
                'fabricante' => $c->fabricante,
            ])->values());
    }

    /**
     * @return array<string, mixed>
     */
    private function selecionadosAtuais(Maquina $maquina): array
    {
        $selecionados = [];

        foreach ([CategoriaComponente::Cpu, CategoriaComponente::PlacaMae, CategoriaComponente::Gpu, CategoriaComponente::Fonte, CategoriaComponente::Gabinete] as $categoria) {
            $selecionados[$categoria->value] = $maquina->componentesDaCategoria($categoria)->first()?->id;
        }

        foreach ([CategoriaComponente::Ram, CategoriaComponente::Armazenamento] as $categoria) {
            $selecionados[$categoria->value] = $maquina->maquinaComponentes
                ->filter(fn ($vinculo) => $vinculo->componente->categoria === $categoria)
                ->map(fn ($vinculo) => ['componente_id' => $vinculo->componente_id, 'quantidade' => $vinculo->quantidade])
                ->values()
                ->all();
        }

        return $selecionados;
    }
}
