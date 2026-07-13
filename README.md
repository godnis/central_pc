# Central PC

Sistema interno de inventário de máquinas (PCs) por setor, construído em Laravel.

Responsáveis: Vitor e Matheus

## Stack atual

- Laravel (Blade + Tailwind + Alpine.js + Vite)
- MySQL/MariaDB
- Autenticação básica (Laravel Breeze-like), sem papéis/permissões

## Estado atual do sistema

Hoje o sistema cadastra **Máquinas** vinculadas a um **Setor**, com campos de texto livre:
processador, memória RAM (número), tipo/capacidade de armazenamento, sistema operacional e observações.
Não existe catálogo de peças, motor de compatibilidade, histórico de alterações, testes automatizados ou API.

## Roadmap — Checklist de melhorias

### 🧩 Catálogo de componentes e compatibilidade (prioridade principal)

- [ ] Criar tabelas estruturadas de componentes por categoria (Processador, Placa-mãe, RAM, Armazenamento, GPU, Fonte, Gabinete) em vez de campos de texto livre
- [ ] Modelar atributos técnicos de cada componente (socket, tipo de memória, form factor, potência, interface de conexão etc.)
- [ ] Motor de compatibilidade: ao selecionar um componente, filtrar automaticamente as opções compatíveis nos campos seguintes
  - [ ] CPU → filtra placas-mãe pelo socket
  - [ ] Placa-mãe → filtra tipo/velocidade de RAM suportada e quantidade de slots
  - [ ] Placa-mãe/Gabinete → valida form factor (ATX, Micro-ATX, ITX)
  - [ ] GPU/Fonte → valida potência mínima recomendada (wattagem)
  - [ ] Armazenamento → filtra por interface suportada pela placa-mãe (SATA, NVMe)
- [ ] Alertar visualmente quando uma combinação incompatível for selecionada (antes de salvar)
- [ ] Permitir montar uma "configuração" de máquina a partir de componentes do catálogo, não apenas texto
- [ ] Tela de administração do catálogo de peças (cadastrar/editar componentes e specs)

### 🖥️ Gestão de máquinas e ativos

- [ ] Histórico de alterações por máquina (quem trocou qual peça, quando)
- [ ] Registro de patrimônio/número de série e etiqueta de identificação (QR code opcional)
- [ ] Upload de foto da máquina
- [ ] Controle de garantia (data de compra, vencimento, fornecedor)
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
- [ ] Testes do motor de compatibilidade (casos de combinações válidas/inválidas)
- [ ] Pipeline de CI (rodar testes e lint a cada push/PR)
- [ ] Padronização de código (Pint/PHP-CS-Fixer, ESLint se necessário)
- [ ] Validação de formulários mais rica no frontend (feedback antes do submit)

### 🚀 Infraestrutura e operação

- [ ] Documentar processo de instalação/deploy no README (setup local, migrations, seeders)
- [ ] Seeders com dados de exemplo (setores e catálogo de componentes)
- [ ] Backups automáticos do banco de dados
- [ ] Ambiente de staging separado de produção
- [ ] Monitoramento de erros (ex: Sentry) e logs estruturados

### 🌐 API e integrações

- [ ] API REST para consulta/gestão de máquinas (uso futuro por outras ferramentas internas)
- [ ] Integração com Active Directory/LDAP para autenticação (se aplicável ao ambiente)
- [ ] Notificações (e-mail/Slack/Teams) para eventos como garantia vencendo ou máquina sem manutenção há muito tempo

### 🎨 Interface

- [ ] Revisão visual geral (identidade profissional, ícones, responsividade)
- [ ] Modo escuro
- [ ] Acessibilidade (labels, contraste, navegação por teclado)
