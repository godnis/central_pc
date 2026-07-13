# Catálogo de componentes + motor de compatibilidade

Data: 2026-07-13
Fase: 1 de N (ver checklist em `README.md`)

## Contexto

Hoje `Maquina` guarda hardware como texto livre: `processador` (string), `memoria_ram_gb` (int),
`tipo_armazenamento` (enum HD/SSD) e `capacidade_armazenamento_gb` (int). Não há GPU, fonte ou
gabinete.

**Correção em relação à primeira versão desta spec:** o banco tem 84 máquinas reais (seed
`MaquinaSeeder`, oriundas de uma planilha de inventário real), não dados de teste descartáveis.
Esta fase inclui uma migração automática desses dados para o novo catálogo (ver seção
"Migração de dados legados").

## Migração de dados legados

Executada como parte das migrations desta fase, antes de remover as colunas antigas de `maquinas`:

- **CPU**: para cada valor distinto de `processador`, cria um `Componente` (`categoria: cpu`,
  `nome: <texto original>`, `specs: {}` vazio — não dá pra inferir `socket` a partir só do nome) e
  vincula à máquina em `maquina_componentes`. Valores repetidos (ex.: várias máquinas com
  "I5-3330") reaproveitam o mesmo `Componente`.
- **RAM**: para cada valor distinto de `memoria_ram_gb` (ignorando nulos), cria um `Componente`
  (`categoria: ram`, `nome: "{gb}GB (genérico, a completar)"`, `specs: {capacidade_gb: gb}`) e
  vincula. Máquinas com `memoria_ram_gb` nulo ficam sem RAM vinculada.
- **Armazenamento**: para cada par distinto `(tipo_armazenamento, capacidade_armazenamento_gb)`,
  cria um `Componente` (`categoria: armazenamento`, `nome: "{tipo} {gb}GB (genérico, a
  completar)"`, `specs: {tipo, capacidade_gb}`) e vincula.
- **Placa-mãe**: não existe campo equivalente hoje, então nenhuma é criada/vinculada. Máquinas
  migradas ficam sem placa-mãe até alguém completar manualmente pela tela de edição (a validação
  de "categoria obrigatória" só se aplica ao salvar via formulário, não retroativamente).
- **GPU**: menções a GPU hoje estão soltas dentro do texto de `observacoes` (ex.: "RTX 3060 /
  192.168.1.8"), misturadas com IPs e notas. Não são extraídas automaticamente — texto arriscado
  de parsear com regex. `observacoes` é mantido como está; quem quiser pode cadastrar a GPU
  manualmente depois.

Os `Componente`s criados por essa migração ficam com `specs` incompletas de propósito (sem
`socket`, `tipos_ram_suportados` etc.) — eles não participam do motor de compatibilidade até
alguém completar as specs pela tela de administração do catálogo. Isso é aceitável: o objetivo
da migração é não perder o dado bruto, não retroativamente validar compatibilidade de máquinas
já existentes.

**Resultado da migração em produção (2026-07-13):** 84 máquinas preservadas, 61 componentes
genéricos criados (31 CPU, 9 RAM, 21 armazenamento), 249 vínculos em `maquina_componentes`.
Backup prévio do banco salvo em `storage/backups/` (fora do controle de versão). Nenhuma placa-mãe
foi criada, como esperado — as 84 máquinas migradas aparecem como "a definir" na coluna
placa-mãe até serem completadas manualmente.

## Objetivo

Ao cadastrar/editar uma máquina, o usuário escolhe componentes de um catálogo e o sistema filtra
automaticamente as opções compatíveis nas categorias seguintes (CPU → placa-mãe → RAM/armazenamento
→ gabinete/fonte), em vez de digitar texto livre.

## Modelo de dados

### `componentes`

| coluna     | tipo                                   | observação                       |
|------------|-----------------------------------------|-----------------------------------|
| id         | bigint pk                              |                                    |
| categoria  | string (enum PHP `CategoriaComponente`)| `cpu`, `placa_mae`, `ram`, `armazenamento`, `gpu`, `fonte`, `gabinete` |
| nome       | string                                 | ex: "Intel Core i5-10400"         |
| fabricante | string, nullable                       |                                    |
| specs      | json                                   | chaves dependem da categoria (ver abaixo) |
| ativo      | boolean, default true                  | permite descontinuar sem apagar   |
| timestamps |                                         |                                    |

Chaves esperadas em `specs` por categoria:

- `cpu`: `socket` (string), `tdp_watts` (int)
- `placa_mae`: `socket` (string), `form_factor` (string), `tipos_ram_suportados` (string[]),
  `interfaces_armazenamento_suportadas` (string[]), `slots_ram` (int)
- `ram`: `tipo` (string, ex DDR4/DDR5), `capacidade_gb` (int), `velocidade_mhz` (int)
- `armazenamento`: `tipo` (string, HD/SSD), `interface` (string, SATA/NVMe), `capacidade_gb` (int)
- `gpu`: `consumo_watts` (int)
- `fonte`: `potencia_watts` (int)
- `gabinete`: `form_factors_suportados` (string[])

### `maquina_componentes`

| coluna        | tipo         | observação                          |
|---------------|--------------|---------------------------------------|
| id            | bigint pk    |                                        |
| maquina_id    | FK maquinas  |                                        |
| componente_id | FK componentes |                                      |
| quantidade    | unsigned int, default 1 | permite 2 pentes de RAM, 2 discos etc. |
| timestamps    |              |                                        |

`Maquina` passa a ter `hasMany(MaquinaComponente::class)`. Os campos `processador`,
`memoria_ram_gb`, `tipo_armazenamento`, `capacidade_armazenamento_gb` são removidos da tabela
`maquinas`. `sistema_operacional` e `observacoes` permanecem (não são hardware).

Categorias obrigatórias no cadastro de máquina: `cpu`, `placa_mae`, `ram`, `armazenamento`.
Opcionais: `gpu`, `fonte`, `gabinete`.

## Motor de compatibilidade

Classe `App\Services\CompatibilidadeService`, regras fixas em código (sem tabela de regras):

1. CPU ↔ Placa-mãe: `cpu.specs.socket === placa.specs.socket`
2. Placa-mãe ↔ RAM: `ram.specs.tipo` está em `placa.specs.tipos_ram_suportados`
3. Placa-mãe ↔ Armazenamento: `armazenamento.specs.interface` está em
   `placa.specs.interfaces_armazenamento_suportadas`
4. Placa-mãe ↔ Gabinete: `placa.specs.form_factor` está em `gabinete.specs.form_factors_suportados`
5. Fonte ↔ CPU + GPU (aviso, não bloqueia): `fonte.specs.potencia_watts >= cpu.specs.tdp_watts +
   soma(gpu.specs.consumo_watts) + 100` (margem fixa de 100W)

Método principal:

```php
CompatibilidadeService::componentesCompativeis(array $selecionados, string $categoriaAlvo): Collection
```

`$selecionados` é um mapa `categoria => Componente` (ou coleção, para categorias com múltiplos itens
como RAM/armazenamento) dos componentes já escolhidos. O método aplica somente as regras relevantes
para `$categoriaAlvo` e retorna os componentes `ativo = true` dessa categoria que passam em todas
elas. Se nenhuma regra se aplica (ex.: `$selecionados` vazio, ou categoria sem relação conhecida,
como GPU sem nada selecionado ainda), retorna todos os componentes ativos da categoria.

A regra de fonte (5) não filtra opções — é avaliada à parte e usada para gerar um aviso textual
("Fonte pode ser insuficiente") exibido na tela, sem bloquear o salvamento.

Validação também roda no backend ao salvar a máquina (`MaquinaController::validarDados` /
`store`/`update`), reaplicando as regras 1–4 sobre os componentes enviados, para não confiar
apenas no filtro client-side.

## API

`POST /componentes/compativeis`

Body: `{ "selecionados": { "cpu": 3, "placa_mae": 7, "ram": [1,2] }, "categoria_alvo": "gabinete" }`

Resposta: `{ "componentes": [{ "id": 12, "nome": "...", "fabricante": "..." }, ...] }`

Rota protegida pelo mesmo middleware `auth` das demais rotas do sistema.

## UI

### Formulário de máquina (`maquinas/_form.blade.php`)

Componente Alpine.js controlando o estado dos selects em ordem: CPU → Placa-mãe → RAM (multi-select
com quantidade) → Armazenamento (multi-select com quantidade) → GPU (opcional) → Fonte (opcional) →
Gabinete (opcional). Cada select, ao mudar, dispara fetch para `/componentes/compativeis` e repopula
o próximo select com as opções compatíveis. Se o usuário mudar uma escolha anterior, os selects
subsequentes que dependem dela são resetados.

Aviso de fonte insuficiente aparece como banner amarelo abaixo dos campos, recalculado a cada
mudança de CPU/GPU/Fonte — não bloqueia o submit.

### Catálogo de componentes (novo CRUD `componentes`)

Novo `ComponenteController` (resource, rotas protegidas por `auth`, mesma convenção das demais).
Formulário com select de `categoria` e campos de specs que mudam dinamicamente via Alpine conforme a
categoria escolhida (em vez de um textarea JSON cru), postando os valores como array que o
controller monta em `specs` antes de salvar.

## Testes

`CompatibilidadeServiceTest` (Feature ou Unit) cobrindo cada regra (1–5) com casos compatível e
incompatível, e o caso de `$selecionados` vazio retornando todos os ativos.

## Fora de escopo desta fase

Papéis/permissões, paginação/busca avançada na listagem de máquinas, histórico de troca de peça,
patrimônio/QR code, API pública, CI, notificações. Essas ficam para fases seguintes do checklist do
`README.md`.
