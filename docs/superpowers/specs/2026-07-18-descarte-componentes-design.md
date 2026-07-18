# Descarte de peças (Anexo 7)

Data: 2026-07-18

## Contexto

Fluxo real do setor de ATI: peças de doação chegam do almoxarifado para teste. As que funcionam
entram no catálogo normal (`componentes`, já existente). As que não funcionam — seja no teste
inicial, seja quando descobertas quebradas durante a montagem de uma máquina — vão para uma
"lixeira preta" física. Quando essa lixeira enche, o setor devolve as peças ao almoxarifado
preenchendo o formulário físico **Anexo 7** ("Entrada e saída de móveis, equipamentos elétrico /
eletrônicos e de informática para avaliação e destinação final"): uma tabela de até 20 linhas por
folha com as colunas Nº, Descrição do material, Retirado por, Data da retirada, Devolvido por,
Data da devolução, Diagnóstico, Destinação final (Local/Data).

O catálogo de `componentes` existente representa **tipos reaproveitáveis** (ex.: "Intel Core
i5-10400"), não unidades físicas individuais, e não tem noção de "peça quebrada". Este é um
subsistema novo e independente, sem ligação com `componentes` ou `maquina_componentes`.

## Objetivo

Cadastrar peças reprovadas numa fila simples, agrupá-las em lotes ("remessas") no momento de
devolução ao almoxarifado, e gerar uma página pronta para impressão com o Anexo 7 já preenchido —
faltando apenas a assinatura manual na coluna "Devolvido por" e o preenchimento de "Destinação
final" pelo almoxarifado.

## Modelo de dados

### `itens_descarte`

| coluna       | tipo                          | observação                                    |
|--------------|-------------------------------|------------------------------------------------|
| id           | bigint pk                     |                                                  |
| descricao    | string                        | texto livre, ex: "Memória RAM DDR3 4GB Kingston" |
| diagnostico  | text                          | por que foi reprovada, ex: "Não liga"           |
| remessa_id   | FK `remessas_descarte`, nullable | `null` = aguardando na fila; preenchido = já despachada |
| user_id      | FK `users`                    | quem cadastrou a peça                           |
| timestamps   |                                |                                                  |

`remessa_id` nulo é o único sinal de "status" — não há coluna de enum separada. Ao ser vinculado a
uma remessa, o item passa a ser imutável (sem edição/remoção).

### `remessas_descarte`

| coluna        | tipo        | observação                                          |
|---------------|-------------|-------------------------------------------------------|
| id            | bigint pk   |                                                         |
| devolvido_por | string      | nome do usuário logado, **congelado** no momento da geração (não segue o usuário se ele for renomeado depois) |
| user_id       | FK `users`  | quem gerou a remessa                                   |
| timestamps    |             | `created_at` é a data da devolução                     |

`RemessaDescarte` tem `hasMany(ItemDescarte::class)`. Sem coluna de "número de lote" — a listagem
usa o `id`/`created_at` para identificar cada remessa.

## Rotas e controllers

Seguindo a convenção das rotas existentes (grupo `auth` + `throttle:60,1`, gate `editar` para
criar/gerar, sem gate `excluir` — não há operação destrutiva sensível aqui):

```
GET    /descarte                    descarte.index            (fila, gate: nenhum extra além de auth)
POST   /descarte                    descarte.store            (gate: editar)
DELETE /descarte/{item}             descarte.destroy          (gate: editar; só permite se remessa_id é null)
POST   /descarte/remessas           descarte.remessas.store   (gate: editar; cria remessa a partir de itens selecionados)
GET    /descarte/remessas           descarte.remessas.index   (histórico, gate: nenhum extra além de auth)
GET    /descarte/remessas/{remessa} descarte.remessas.show    (página de impressão, gate: nenhum extra além de auth)
```

Dois controllers: `ItemDescarteController` (index/store/destroy) e `RemessaDescarteController`
(store/index/show). Sem `edit`/`update` em nenhum dos dois — peça errada se remove e recadastra;
remessa fechada é imutável.

### `ItemDescarteController::store`

Validação: `descricao` (required, string, max:255), `diagnostico` (required, string, max:1000).
Cria o item com `user_id` do usuário logado, registra `Atividade::registrar($item, 'criado', ...)`,
redireciona para `itens_descarte.index`.

### `ItemDescarteController::destroy`

Só permite excluir se `remessa_id` é `null` (item ainda na fila); se já estiver numa remessa,
redireciona com mensagem de erro (mesmo padrão do `SetorController::destroy` ao bloquear exclusão
de setor com máquinas). Registra `Atividade::registrar($item, 'excluido', ...)` antes de apagar.

### `RemessaDescarteController::store`

Recebe `itens: int[]` (IDs selecionados na fila). Validação: array não vazio, todos os IDs devem
existir em `itens_descarte` com `remessa_id` nulo (`Rule::exists(...)->where('remessa_id', null)`).
Dentro de uma transação: cria a `RemessaDescarte` com `devolvido_por = auth()->user()->name`,
atualiza `remessa_id` de todos os itens selecionados, registra
`Atividade::registrar($remessa, 'criado', "Remessa de descarte gerada com N peça(s).")`. Redireciona
para `remessas_descarte.show`.

## UI

### Fila de descarte (`descarte/index.blade.php`)

Lista os itens com `remessa_id` nulo, mais recentes primeiro. Cada linha: checkbox, descrição,
diagnóstico, data de cadastro, botão "Remover". Acima da lista, botão "+ Nova peça" (abre
`descarte/create.blade.php`, card simples com os dois campos, no mesmo estilo de card usado nos
formulários de setor/componente). Abaixo da lista, botão "Gerar Anexo 7 com selecionados"
(desabilitado se nenhum checkbox marcado), que faz `POST remessas_descarte.store` com os IDs
marcados: o `<form>` tem `target="_blank"`, então o redirect do controller para a página de
impressão abre direto numa nova aba, mantendo a fila aberta na aba original. Link "Ver remessas
anteriores →" no topo da página, apontando para `remessas_descarte.index`.

### Histórico de remessas (`descarte/remessas/index.blade.php`)

Tabela simples: data, devolvido por, quantidade de itens, link "Ver / Imprimir" para
`remessas_descarte.show`.

### Página de impressão (`descarte/remessas/show.blade.php`)

Layout **próprio**, fora do `x-app-layout` (sem sidebar) — só o cabeçalho do Anexo 7 e a tabela,
para não poluir a impressão. Reproduz a grade do formulário físico:

- Cabeçalho: logo (mesma logo usada no restante do sistema) + "ANEXO 7" + título "ALMOXARIFADO —
  Entrada e saída de móveis, equipamentos elétrico/eletrônicos e de informática para avaliação e
  destinação final".
- Tabela com colunas Nº, Descrição do material, Retirado por, Data da retirada, Devolvido por, Data
  da devolução, Diagnóstico, Destinação final (Local | Data).
- Uma linha por item da remessa: Nº = posição na página (reinicia em 1 a cada 20 linhas), Descrição
  = `item.descricao`, Retirado por / Data da retirada = em branco, Devolvido por =
  `remessa.devolvido_por`, Data da devolução = `remessa.created_at` formatada, Diagnóstico =
  `item.diagnostico`, Destinação final = em branco (ambas as colunas).
- Se a remessa tiver mais de 20 itens, quebra em múltiplas páginas de impressão
  (`break-after: page` em CSS a cada 20ª linha); o `<thead>` da tabela se repete automaticamente em
  cada página impressa (comportamento nativo do navegador).
- Preenche com linhas em branco até completar a última folha de 20, igual ao formulário físico.
- Botão "Imprimir" (`window.print()`), oculto via `@media print { .no-print { display: none } }`.

### Navegação

Novo item "Descarte" na sidebar (`navigation.blade.php`), dentro do grupo "Inventário", ao lado de
Máquinas/Setores/Componentes, apontando para `itens_descarte.index`.

## Permissões

`editar` (Admin + Técnico) para cadastrar peça, remover da fila e gerar remessa — mesma gate usada
em Máquinas/Setores/Componentes. Visualização (fila, histórico, página de impressão) livre para
qualquer usuário autenticado, incluindo `Leitura`.

## Testes

`Feature/DescarteTest.php` cobrindo: cadastro de item válido, cadastro rejeita descrição/diagnóstico
vazios, remoção de item da fila funciona, remoção de item já em remessa é bloqueada, geração de
remessa exige ao menos um item selecionado, geração de remessa marca os itens corretos e congela
`devolvido_por`, página de impressão de uma remessa com 25 itens gera 2 páginas (verificar contagem
de `break-after` ou de blocos de 20 linhas no HTML renderizado).

## Fora de escopo

- Ligação com o catálogo `componentes` (peças descartadas não precisam existir no catálogo).
- Rastreio de "retirado do almoxarifado" (data/quem) — não existe hoje, colunas ficam em branco.
- Preenchimento de "Destinação final" pelo sistema — é decisão do almoxarifado, feita à mão.
- Edição de item já cadastrado ou de remessa já gerada.
- Geração de PDF via biblioteca (dompdf etc.) — a impressão é feita pelo navegador (Ctrl+P / Salvar
  como PDF), sem depender de nenhuma dependência nova no projeto.
- Notificação/alerta de "lixeira cheia" — o usuário decide manualmente quando gerar a remessa.
