# Central PC

Sistema interno de inventário de máquinas (PCs) por setor, construído em Laravel.

Responsáveis: Vitor e Matheus

## Stack atual

- Laravel (Blade + Tailwind + Alpine.js + Vite)
- MySQL/MariaDB
- Autenticação básica (Laravel Breeze-like), sem papéis/permissões

## Estado atual do sistema

O sistema cadastra **Máquinas** vinculadas a um **Setor**, com hardware montado a partir de um
**catálogo estruturado de Componentes** (CPU, placa-mãe, RAM, armazenamento, GPU, fonte, gabinete)
com motor de compatibilidade automático (ver [spec](docs/superpowers/specs/2026-07-13-catalogo-componentes-compatibilidade-design.md)).
Ainda não há histórico de alterações, papéis/permissões, testes de feature/CI, ou API.

## Roadmap — Checklist de melhorias

### 🧩 Catálogo de componentes e compatibilidade (prioridade principal) — ✅ fase 1 entregue

- [x] Criar tabelas estruturadas de componentes por categoria (Processador, Placa-mãe, RAM, Armazenamento, GPU, Fonte, Gabinete) em vez de campos de texto livre
- [x] Modelar atributos técnicos de cada componente (socket, tipo de memória, form factor, potência, interface de conexão etc.)
- [x] Motor de compatibilidade: ao selecionar um componente, filtrar automaticamente as opções compatíveis nos campos seguintes
  - [x] CPU → filtra placas-mãe pelo socket
  - [x] Placa-mãe → filtra tipo de RAM suportada (validação de *quantidade* de slots ainda não é aplicada)
  - [x] Placa-mãe/Gabinete → valida form factor (ATX, Micro-ATX, ITX)
  - [x] GPU/Fonte → valida potência mínima recomendada (wattagem, como aviso não bloqueante)
  - [x] Armazenamento → filtra por interface suportada pela placa-mãe (SATA, NVMe)
- [x] Alertar visualmente quando uma combinação incompatível for selecionada (antes de salvar)
- [x] Permitir montar uma "configuração" de máquina a partir de componentes do catálogo, não apenas texto
- [x] Tela de administração do catálogo de peças (cadastrar/editar componentes e specs)

### 🖥️ Gestão de máquinas e ativos

- [ ] Histórico de alterações por máquina (quem trocou qual peça, quando)
- [ ] Registro de patrimônio/número de série e etiqueta de identificação (QR code opcional)
- [ ] Upload de foto da máquina
- [ ] Status da máquina (ativa, em manutenção, baixada/descartada)
- [ ] Vínculo opcional com usuário/colaborador responsável pela máquina
- [ ] Soft delete (não apagar máquina definitivamente, permitir restaurar)

### 🔍 Usabilidade e busca

- [ ] Busca e filtros avançados na listagem (por setor, componente, status, sistema operacional)
- [ ] Paginação na listagem de máquinas
- [ ] Ordenação de colunas na tabela
- [ ] Exportação de relatórios (PDF/Excel/CSV)
- [ ] Dashboard com indicadores (total de máquinas, por setor, por status, idade média do parque)

### 🔐 Segurança e permissões

- [ ] Papéis de usuário (admin, técnico, leitura) com permissões (ex: Spatie Laravel-Permission)
- [ ] Log de auditoria (criação/edição/exclusão) com autor e timestamp
- [ ] Confirmação de exclusão com proteção contra ações acidentais
- [ ] Rate limiting e proteção de rotas sensíveis
- [ ] Revisão de `.env` e segredos (garantir que nada sensível vá para o repositório)

### 🧪 Qualidade e testes

- [ ] Testes automatizados (Feature/Unit) para os fluxos de CRUD de Máquinas e Setores
- [x] Testes do motor de compatibilidade (casos de combinações válidas/inválidas)
- [ ] Pipeline de CI (rodar testes e lint a cada push/PR)
- [ ] Padronização de código (Pint/PHP-CS-Fixer, ESLint se necessário)
- [ ] Validação de formulários mais rica no frontend (feedback antes do submit)

### 🚀 Infraestrutura e operação

- [ ] Documentar processo de instalação/deploy no README (setup local, migrations, seeders)
- [x] Seeders com dados de exemplo (setores e catálogo de componentes)
- [ ] Backups automáticos do banco de dados
- [ ] Ambiente de staging separado de produção
- [ ] Monitoramento de erros (ex: Sentry) e logs estruturados

### 🎨 Interface

- [ ] Revisão visual geral (identidade profissional, ícones, responsividade)
- [ ] Modo escuro
- [ ] Acessibilidade (labels, contraste, navegação por teclado)
