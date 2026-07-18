# Auditoria e organização do banco de dados

Data: 2026-07-18

## Contexto

Levantamento de todos os bancos de dados acessíveis nos projetos em `htdocs`: o único banco local
sob controle direto e com dados reais é o Postgres do Central PC (`central_pc`, 127.0.0.1:5432).
Os demais projetos (`totem` aponta para um Postgres remoto de terceiros; `restaurante-saas` espera
um Postgres local na porta 5433 que não está em execução; `dashboard`/`matheus` não têm banco) ficam
fora de escopo.

Auditoria do `central_pc` encontrou dois pontos de melhoria concretos, escolhidos pelo usuário entre
as opções levantadas (performance, backup, documentação, limpeza de dados):

1. **Nenhuma coluna de chave estrangeira tem índice próprio.** Diferente do MySQL, o Postgres não
   cria índice automaticamente ao criar uma FK. Com o volume atual (dezenas a poucas centenas de
   linhas por tabela) isso não pesa, mas é uma lacuna que compensa fechar agora.
2. **Não existe documentação do esquema.** Backups manuais avulsos já existem em
   `storage/backups/` (fora de escopo aqui — não foi a opção escolhida), mas não há nenhum
   documento de referência explicando as tabelas e seus relacionamentos.

## Índices

Migration única `add_missing_foreign_key_indexes_table`, adicionando índice simples nas colunas de
FK que hoje não têm nenhum:

| Tabela                | Coluna         |
|------------------------|----------------|
| `maquinas`             | `setor_id`     |
| `maquina_componentes`  | `maquina_id`   |
| `maquina_componentes`  | `componente_id`|
| `atividades`           | `user_id`      |
| `itens_descarte`       | `user_id`      |
| `itens_descarte`       | `remessa_id`   |
| `remessas_descarte`    | `user_id`      |

Todas via `$table->index('coluna')` em `Schema::table(...)`, com `down()` removendo os mesmos
índices (`dropIndex`). Não mexe em dados, constraints existentes ou colunas — só adiciona os
índices. Sem risco de quebra; roda em produção sem downtime perceptível dado o volume atual.

Colunas já cobertas por índice existente (fora do escopo desta migration, nada a fazer):
`atividades.(loggable_type, loggable_id)` (composto, já existe), `maquinas.patrimonio` (unique já
existe), toda `_pkey`.

## Documentação do esquema

Novo arquivo `docs/banco-de-dados.md` (referência viva, fora de `docs/superpowers/specs` porque não
é uma spec de feature — é documentação de estado atual, mantida atualizada conforme o esquema
mudar). Conteúdo:

1. **Diagrama ER em Mermaid** (` ```mermaid erDiagram ` ) cobrindo as 8 tabelas de domínio
   (`setores`, `maquinas`, `componentes`, `maquina_componentes`, `users`, `atividades`,
   `itens_descarte`, `remessas_descarte`) com suas relações (1:N) e chaves.
2. **Uma seção por tabela de domínio**: propósito em uma frase, colunas que não são autoexplicativas
   (ex.: `componentes.specs` é JSON com formato dependente da `categoria`, `componentes.ativo`
   controla disponibilidade no cadastro de máquina sem apagar histórico, `atividades` é polimórfica
   via `loggable_type`/`loggable_id`, `itens_descarte.remessa_id` nulo = ainda na fila), e suas FKs.
3. **Uma nota curta** dispensando as tabelas de infraestrutura do Laravel (`sessions`, `cache`,
   `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `migrations`, `password_reset_tokens`,
   `personal_access_tokens`) sem documentá-las campo a campo — são geradas pelo framework, não pela
   regra de negócio do Central PC.

Não é gerado automaticamente a partir do banco (sem tooling de schema-diff no projeto) — é escrito
à mão a partir do estado atual das migrations, e deve ser atualizado manualmente quando o esquema
mudar (mesma disciplina de manter `docs/superpowers/specs/` atualizado por feature).

## Testes

Nenhum teste automatizado dedicado — a migration de índices é validada rodando
`php artisan migrate` e `php artisan migrate:rollback` localmente antes de considerar concluído, e
conferindo via `pg_indexes` que os 7 índices foram criados. Documentação não é testável
automaticamente; revisão manual do conteúdo é suficiente.

## Fora de escopo

Backup automatizado, limpeza de dados legados (componentes "genérico, a completar" da migração
antiga, máquinas antigas na lixeira), qualquer ação sobre os bancos de `totem` (remoto, fora do
nosso controle) ou `restaurante-saas` (não está em execução).
