# Central PC

Sistema interno de inventário de máquinas (PCs) por setor, construído em Laravel.

Responsáveis: Vitor e Matheus

## Stack atual

- Laravel 12 (Blade + Tailwind + Alpine.js + Vite)
- PostgreSQL
- Autenticação por sessão (Laravel Breeze) com papéis (admin/técnico/leitura)
- API REST autenticada por token (Laravel Sanctum)

## Estado atual do sistema

O sistema cadastra **Máquinas** vinculadas a um **Setor**, com hardware montado a partir de um
**catálogo estruturado de Componentes** (CPU, placa-mãe, RAM, armazenamento, GPU, fonte, gabinete)
com motor de compatibilidade automático (ver [spec](docs/superpowers/specs/2026-07-13-catalogo-componentes-compatibilidade-design.md)).
Cada máquina tem patrimônio, status, foto, QR code de identificação, responsável, histórico de
alterações e soft delete (lixeira). Há papéis de usuário, log de auditoria, testes automatizados,
CI, API REST e alerta de manutenção preventiva por e-mail.

## Instalação

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate
# edite .env: DB_* (Postgres), MAIL_*, e opcionalmente SENTRY_LARAVEL_DSN / NOTIFICACOES_WEBHOOK_URL

php artisan migrate
php artisan db:seed          # setores + catálogo de exemplo + inventário de máquinas
php artisan storage:link     # necessário para exibir fotos de máquina

npm run build                # ou `npm run dev` durante desenvolvimento
php artisan serve
```

Login padrão do seeder: `admin@central.local` / `password` — **troque a senha assim que entrar**.

### Backups e alertas automáticos (produção)

Os comandos `php artisan backup:database` (dump diário do Postgres via `pg_dump`, requer o
binário no `PATH`) e `php artisan app:verificar-parque` (alerta semanal por e-mail sobre
máquinas antigas/em manutenção prolongada) só rodam sozinhos se o
[scheduler do Laravel](https://laravel.com/docs/scheduling) estiver registrado no cron do
servidor:

```
* * * * * cd /caminho/do/projeto && php artisan schedule:run >> /dev/null 2>&1
```

### Segurança em produção

- Configure `APP_ENV=production` e `APP_DEBUG=false` (nunca deixe debug ligado em produção —
  ele expõe stack traces e variáveis de ambiente).
- `.env` nunca é versionado (já está no `.gitignore`); `.env.example` não tem segredos reais.
- Opcional: defina `SENTRY_LARAVEL_DSN` para monitoramento de erros e `NOTIFICACOES_WEBHOOK_URL`
  (Slack/Teams) para os alertas de manutenção — sem eles, esses recursos ficam inativos (no-op),
  não quebram nada.

## Roadmap — Checklist de melhorias

### 🧩 Catálogo de componentes e compatibilidade — ✅ concluído

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

### 🖥️ Gestão de máquinas e ativos — ✅ concluído (exceto garantia)

- [x] Histórico de alterações por máquina (quem trocou qual peça, quando) — aba de histórico na tela de detalhe
- [x] Registro de patrimônio e etiqueta de identificação (QR code apontando pra tela de detalhe)
- [x] Upload de foto da máquina
- [ ] Controle de garantia (data de compra, vencimento, fornecedor) — só existe `data_aquisicao`; faltam `vencimento` e `fornecedor`
- [x] Status da máquina (ativa, em manutenção, baixada)
- [x] Vínculo opcional com responsável pela máquina (campo de texto livre — não é uma conta de usuário)
- [x] Soft delete com lixeira (restaurar ou excluir definitivamente)

### 🔍 Usabilidade e busca — ✅ concluído (exceto PDF/Excel)

- [x] Busca e filtros avançados na listagem (nome, patrimônio, SO, componente, setor, status)
- [x] Paginação na listagem de máquinas
- [x] Ordenação de colunas na tabela
- [x] Exportação em CSV — PDF/Excel não implementados (adicionariam dependências extras; CSV já abre em Excel)
- [x] Dashboard com indicadores (total, por status, por setor, idade média do parque)

### 🔐 Segurança e permissões — ✅ concluído

- [x] Papéis de usuário (admin, técnico, leitura) com permissões — implementado com Gates nativos do Laravel (não usa Spatie Laravel-Permission, pra evitar dependência extra num modelo de 3 papéis fixos)
- [x] Log de auditoria (criação/edição/exclusão de máquinas, setores e componentes) com autor e timestamp
- [x] Confirmação de exclusão com proteção contra ações acidentais
- [x] Rate limiting (throttle nas rotas autenticadas + limite de tentativas de login já existente)
- [x] Revisão de `.env` e segredos — nenhum segredo real em `.env.example`; recomendações de produção documentadas acima

### 🧪 Qualidade e testes — ✅ concluído (exceto validação JS rica)

- [x] Testes automatizados (Feature) para os fluxos de CRUD de Máquinas e Setores, incluindo papéis de usuário
- [x] Testes do motor de compatibilidade (casos de combinações válidas/inválidas)
- [x] Pipeline de CI (GitHub Actions: composer install, migrations, Pint, testes a cada push/PR)
- [x] Padronização de código (Laravel Pint)
- [ ] Validação de formulários mais rica no frontend — hoje só validação HTML5 nativa (`required`, `type`); sem feedback JS em tempo real

### 🚀 Infraestrutura e operação — ✅ concluído (exceto staging)

- [x] Documentar processo de instalação/deploy no README (seção Instalação acima)
- [x] Seeders com dados de exemplo (setores e catálogo de componentes)
- [x] Backup automático do banco (`php artisan backup:database`, agendável via scheduler)
- [ ] Ambiente de staging separado de produção — decisão de infraestrutura/hosting, fora do escopo de código
- [x] Monitoramento de erros — Sentry integrado e pronto (requer `SENTRY_LARAVEL_DSN` real em produção pra ativar)

### 🌐 API e integrações — ✅ concluído (exceto LDAP)

- [x] API REST (Laravel Sanctum) para Máquinas, Setores e Componentes — autenticação por token, respeitando os mesmos papéis/permissões da interface web. Gerenciamento de tokens em **Tokens de API** no menu (admin)
- [ ] Integração com Active Directory/LDAP — depende de dados do servidor AD da organização (host, base DN etc.) que não estavam disponíveis; não implementado
- [x] Notificações — comando semanal (`app:verificar-parque`) envia e-mail aos admins sobre máquinas com mais de 3 anos de uso ou em manutenção há mais de 30 dias, com webhook opcional (Slack/Teams) via `NOTIFICACOES_WEBHOOK_URL`

### 🎨 Interface — ✅ concluído

- [x] Revisão visual geral (dashboard com indicadores, badges de status, ícones, layout responsivo)
- [x] Modo escuro (alternável, persistido, aplicado em toda a interface)
- [x] Acessibilidade — link "pular para o conteúdo", labels em todos os campos, `aria-label` em controles de ícone, foco visível; não é uma auditoria completa de WCAG

## O que ainda depende de decisão sua

- **LDAP/Active Directory**: preciso do host, base DN e credenciais do seu servidor AD (se existir) pra implementar.
- **Ambiente de staging**: preciso saber se há um segundo servidor/subdomínio disponível.
- **Sentry**: crie uma conta em sentry.io e cole o DSN em `SENTRY_LARAVEL_DSN` — o código já está pronto.
- **Webhook do Slack/Teams**: cole a URL em `NOTIFICACOES_WEBHOOK_URL` quando tiver uma.
- **Controle de garantia**: se quiser rastrear vencimento e fornecedor, é rápido de adicionar — só não inventei esses campos sem saber se fazem sentido pro seu processo de compras.
