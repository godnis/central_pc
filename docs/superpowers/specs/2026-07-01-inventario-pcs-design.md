# Sistema de Inventário de PCs — Design

## Contexto e objetivo

Sistema interno para registrar e contar todas as máquinas (PCs) da empresa, organizadas por setor. Para cada máquina, guardar: sistema operacional, processador, quantidade de RAM, tipo de armazenamento (HD/SSD) e capacidade de armazenamento.

Requisito explícito do usuário: o sistema deve ser **pequeno e simples**, pois as linhas de código precisam ser explicadas depois. Isso guia várias decisões de escopo abaixo — preferimos menos arquivos e menos abstrações a "boas práticas" que adicionariam complexidade sem necessidade real neste tamanho de projeto.

## Stack

- **Laravel** (versão estável mais recente disponível via Composer)
- **PostgreSQL** como banco de dados (já instalado e rodando na máquina do usuário)
- **Blade** para as views — sem Vue/React/Livewire
- **Laravel Breeze** (stack Blade) para autenticação

## Autenticação

- Login/logout via Breeze.
- **Rota e view de registro removidas** — não deve ser possível se autocadastrar. Usuários são criados por quem administra o sistema, via `php artisan tinker` ou um seeder.
- **Fluxo de "esqueci minha senha" removido** — depende de configuração de envio de e-mail (SMTP), fora do escopo deste sistema pequeno. Pode ser adicionado depois se necessário.
- Todas as rotas do sistema (exceto `/login`) protegidas pelo middleware `auth`.

## Modelo de dados

### `users` (gerada pelo Breeze)
Campos padrão: `name`, `email`, `password`.

### `setores`
| Campo | Tipo |
|---|---|
| `nome` | string |

### `maquinas`
| Campo | Tipo |
|---|---|
| `nome` | string (identificação da máquina, ex: "PC-Financeiro-01") |
| `setor_id` | foreign key → `setores` |
| `sistema_operacional` | string |
| `processador` | string |
| `memoria_ram_gb` | integer |
| `tipo_armazenamento` | enum: `HD` ou `SSD` |
| `capacidade_armazenamento_gb` | integer |

### Relacionamentos
- `Setor` **hasMany** `Maquina`
- `Maquina` **belongsTo** `Setor`

## Telas e navegação

1. **Login** (`/login`) — tela do Breeze, sem link de registro.
2. **Máquinas** (`/maquinas`) — tela principal pós-login:
   - Cards de resumo no topo: total geral de máquinas e total por setor.
   - Tabela listando todas as máquinas (nome, setor, SO, processador, RAM, tipo e capacidade de armazenamento).
   - Filtro por setor.
   - Ações: criar, editar, excluir (exclusão com confirmação simples via `confirm()` do navegador).
3. **Formulário de Máquina** (criar/editar, `/maquinas/create`, `/maquinas/{id}/edit`) — um formulário para todos os campos do modelo `Maquina`; setor escolhido via `<select>` populado com os setores cadastrados.
4. **Setores** (`/setores`) — listagem simples de setores + criar/editar/excluir.
5. **Formulário de Setor** (criar/editar) — campo único: `nome`.

Rota raiz (`/`) redireciona para `/maquinas` quando autenticado.

## Controllers e rotas

- `Route::resource('maquinas', MaquinaController::class)`
- `Route::resource('setores', SetorController::class)`
- Ambos os resource controllers protegidos pelo middleware `auth` (via grupo de rotas ou `$this->middleware('auth')` no construtor).

## Validação

Validação inline nos métodos `store`/`update` de cada controller, usando `$request->validate([...])`. Sem classes `FormRequest` separadas — mantém a lógica de validação junto ao código que a usa, mais fácil de ler e explicar em um único lugar.

## Seeding inicial

- Um seeder para o(s) usuário(s) que podem acessar o sistema (já que não há autocadastro).
- Um seeder para os setores da empresa — a lista de setores será fornecida pelo usuário (via imagem) durante a implementação.

## Testes

Sem suíte de testes automatizados nesta primeira versão. Verificação será manual, pelo navegador, durante o desenvolvimento. Pode ser adicionada em uma iteração futura caso necessário.

## Fora de escopo (nesta versão)

- Autocadastro de usuários
- Recuperação de senha por e-mail
- API / frontend separado
- Testes automatizados
