<?php

namespace App\Http\Controllers;

use App\Enums\CategoriaComponente;
use App\Enums\StatusMaquina;
use App\Http\Controllers\Concerns\GerenciaComponentesDaMaquina;
use App\Models\Atividade;
use App\Models\Componente;
use App\Models\Maquina;
use App\Models\Setor;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MaquinaController extends Controller
{
    use GerenciaComponentesDaMaquina;

    private const COLUNAS_ORDENAVEIS = ['nome', 'patrimonio', 'status', 'created_at'];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $setorId = $request->query('setor_id');
        $status = $request->query('status');
        $busca = trim((string) $request->query('busca'));
        $sort = in_array($request->query('sort'), self::COLUNAS_ORDENAVEIS, true) ? $request->query('sort') : 'nome';
        $dir = $request->query('dir') === 'desc' ? 'desc' : 'asc';

        $maquinas = Maquina::with(['setor', 'maquinaComponentes.componente'])
            ->when($setorId, fn ($query) => $query->where('setor_id', $setorId))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($busca !== '', function ($query) use ($busca) {
                $query->where(function ($query) use ($busca) {
                    $termo = '%'.mb_strtolower($busca).'%';
                    $query->whereRaw('LOWER(nome) LIKE ?', [$termo])
                        ->orWhereRaw('LOWER(patrimonio) LIKE ?', [$termo])
                        ->orWhereRaw('LOWER(sistema_operacional) LIKE ?', [$termo])
                        ->orWhereHas('maquinaComponentes.componente', function ($query) use ($termo) {
                            $query->whereRaw('LOWER(nome) LIKE ?', [$termo]);
                        });
                });
            })
            ->orderBy($sort, $dir)
            ->paginate(20)
            ->withQueryString();

        $totalGeral = Maquina::count();

        $totalPorSetor = Setor::withCount('maquinas')
            ->get()
            ->filter(fn ($setor) => $setor->maquinas_count > 0)
            ->sortByDesc('maquinas_count');

        $totalPorStatus = Maquina::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Calculado em PHP (não em SQL) para funcionar igual em Postgres e SQLite (testes).
        $mediaEmDias = Maquina::whereNotNull('data_aquisicao')->pluck('data_aquisicao')
            ->avg(fn ($data) => $data->diffInDays(now()));
        $idadeMediaAnos = $mediaEmDias ? round($mediaEmDias / 365, 1) : null;

        $setores = Setor::orderBy('nome')->get();
        $statusList = StatusMaquina::cases();

        return view('maquinas.index', compact(
            'maquinas', 'totalGeral', 'totalPorSetor', 'totalPorStatus', 'idadeMediaAnos',
            'setores', 'setorId', 'status', 'busca', 'sort', 'dir', 'statusList'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show(Maquina $maquina): View
    {
        $maquina->load(['setor', 'maquinaComponentes.componente', 'atividades.user']);

        return view('maquinas.show', compact('maquina'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $setores = Setor::orderBy('nome')->get();
        $componentesPorCategoria = $this->componentesAtivosPorCategoria();
        $statusList = StatusMaquina::cases();
        $maquina = new Maquina;

        return view('maquinas.create', compact('setores', 'componentesPorCategoria', 'statusList', 'maquina'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $dados = $this->validarDados($request);
        $this->validarCompatibilidade($dados['componentes']);

        $maquina = Maquina::create($this->dadosDaMaquina($dados, $request));
        $this->sincronizarComponentes($maquina, $dados['componentes']);
        Atividade::registrar($maquina, 'criado', "Máquina \"{$maquina->nome}\" cadastrada.");

        return redirect()->route('maquinas.index')->with('status', 'Máquina cadastrada com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Maquina $maquina): View
    {
        $setores = Setor::orderBy('nome')->get();
        $componentesPorCategoria = $this->componentesAtivosPorCategoria();
        $statusList = StatusMaquina::cases();

        $maquina->load('maquinaComponentes.componente');
        $selecionadosAtuais = $this->selecionadosAtuais($maquina);

        return view('maquinas.edit', compact(
            'maquina', 'setores', 'componentesPorCategoria', 'selecionadosAtuais', 'statusList'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Maquina $maquina): RedirectResponse
    {
        $dados = $this->validarDados($request, $maquina);
        $this->validarCompatibilidade($dados['componentes']);

        $componentesAntes = $maquina->maquinaComponentes->pluck('quantidade', 'componente_id');

        $maquina->update($this->dadosDaMaquina($dados, $request, $maquina));
        $this->sincronizarComponentes($maquina, $dados['componentes']);

        $componentesDepois = $maquina->maquinaComponentes()->get()->pluck('quantidade', 'componente_id');
        $descricao = $componentesAntes->toArray() === $componentesDepois->toArray()
            ? "Dados de \"{$maquina->nome}\" atualizados."
            : "Dados de \"{$maquina->nome}\" atualizados (componentes alterados).";
        Atividade::registrar($maquina, 'atualizado', $descricao);

        return redirect()->route('maquinas.index')->with('status', 'Máquina atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Maquina $maquina): RedirectResponse
    {
        Atividade::registrar($maquina, 'excluido', "Máquina \"{$maquina->nome}\" movida para a lixeira.");
        $maquina->delete();

        return redirect()->route('maquinas.index')->with('status', 'Máquina excluída com sucesso.');
    }

    /**
     * Lista as máquinas excluídas (soft delete), para restaurar ou excluir de vez.
     */
    public function lixeira(): View
    {
        $maquinas = Maquina::onlyTrashed()->with('setor')->orderByDesc('deleted_at')->get();

        return view('maquinas.lixeira', compact('maquinas'));
    }

    public function restaurar(int $id): RedirectResponse
    {
        $maquina = Maquina::onlyTrashed()->findOrFail($id);
        $maquina->restore();
        Atividade::registrar($maquina, 'atualizado', "Máquina \"{$maquina->nome}\" restaurada da lixeira.");

        return redirect()->route('maquinas.lixeira')->with('status', 'Máquina restaurada com sucesso.');
    }

    public function excluirDefinitivamente(int $id): RedirectResponse
    {
        $maquina = Maquina::onlyTrashed()->findOrFail($id);

        if ($maquina->foto_path) {
            Storage::disk('public')->delete($maquina->foto_path);
        }

        $maquina->maquinaComponentes()->delete();
        $nome = $maquina->nome;
        $maquina->forceDelete();

        return redirect()->route('maquinas.lixeira')
            ->with('status', "Máquina \"{$nome}\" excluída definitivamente.");
    }

    /**
     * Exporta a listagem filtrada em CSV.
     */
    public function export(Request $request): Response
    {
        $setorId = $request->query('setor_id');
        $status = $request->query('status');

        $maquinas = Maquina::with(['setor', 'maquinaComponentes.componente'])
            ->when($setorId, fn ($query) => $query->where('setor_id', $setorId))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderBy('nome')
            ->get();

        $linhas = [['Nome', 'Patrimônio', 'Setor', 'Status', 'SO', 'Processador', 'Placa-mãe', 'RAM', 'Armazenamento', 'Responsável', 'Observações']];

        foreach ($maquinas as $maquina) {
            $linhas[] = [
                $maquina->nome,
                $maquina->patrimonio,
                $maquina->setor->nome,
                $maquina->status->label(),
                $maquina->sistema_operacional,
                $maquina->componentesDaCategoria(CategoriaComponente::Cpu)->pluck('nome')->join(', '),
                $maquina->componentesDaCategoria(CategoriaComponente::PlacaMae)->pluck('nome')->join(', '),
                $maquina->componentesDaCategoria(CategoriaComponente::Ram)->pluck('nome')->join(', '),
                $maquina->componentesDaCategoria(CategoriaComponente::Armazenamento)->pluck('nome')->join(', '),
                $maquina->responsavel,
                $maquina->observacoes,
            ];
        }

        $csv = fopen('php://memory', 'w');
        fwrite($csv, "\xEF\xBB\xBF"); // BOM, para acentos abrirem certo no Excel
        foreach ($linhas as $linha) {
            fputcsv($csv, $linha, ';');
        }
        rewind($csv);
        $conteudo = stream_get_contents($csv);
        fclose($csv);

        return response($conteudo, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="maquinas_'.now()->format('Y-m-d').'.csv"',
        ]);
    }

    /**
     * Gera um QR code (SVG) que aponta para a página de detalhe da máquina —
     * útil para colar uma etiqueta física no equipamento.
     */
    public function qrcode(Maquina $maquina): Response
    {
        $resultado = (new Builder(
            writer: new SvgWriter,
            data: route('maquinas.show', $maquina),
            size: 220,
            margin: 8,
        ))->build();

        return response($resultado->getString(), 200, [
            'Content-Type' => $resultado->getMimeType(),
        ]);
    }

    /**
     * Valida os dados do formulário de máquina, incluindo os componentes
     * selecionados do catálogo.
     */
    private function validarDados(Request $request, ?Maquina $maquina = null): array
    {
        return $request->validate([
            'nome' => 'required|string|max:255',
            'patrimonio' => ['nullable', 'string', 'max:100', Rule::unique('maquinas', 'patrimonio')->ignore($maquina?->id)],
            'setor_id' => 'required|exists:setores,id',
            'status' => ['required', Rule::enum(StatusMaquina::class)],
            'sistema_operacional' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
            'responsavel' => 'nullable|string|max:255',
            'data_aquisicao' => 'nullable|date',
            'foto' => 'nullable|image|max:4096',
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

    private function dadosDaMaquina(array $dados, Request $request, ?Maquina $maquina = null): array
    {
        $dadosMaquina = [
            'nome' => $dados['nome'],
            'patrimonio' => $dados['patrimonio'] ?? null,
            'setor_id' => $dados['setor_id'],
            'status' => $dados['status'],
            'sistema_operacional' => $dados['sistema_operacional'] ?? null,
            'observacoes' => $dados['observacoes'] ?? null,
            'responsavel' => $dados['responsavel'] ?? null,
            'data_aquisicao' => $dados['data_aquisicao'] ?? null,
        ];

        if ($request->hasFile('foto')) {
            if ($maquina?->foto_path) {
                Storage::disk('public')->delete($maquina->foto_path);
            }
            $dadosMaquina['foto_path'] = $request->file('foto')->store('maquinas', 'public');
        }

        return $dadosMaquina;
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
