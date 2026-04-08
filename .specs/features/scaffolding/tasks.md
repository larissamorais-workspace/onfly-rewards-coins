# Scaffolding Tasks

**Design**: `.specs/features/scaffolding/design.md`
**Status**: Approved

---

## Execution Plan

### Phase 1 — Foundation (Sequential)
Pré-requisito para tudo. Deve rodar em ordem.

```
T1 → T2 → T3
```

### Phase 2 — Backend Core (Parallel após T3)
Models, migrations e seeders independentes entre si.

```
T3 complete, então:
  ├── T4  [P] Migration: companies + users
  ├── T5  [P] Migration: travel_policies
  ├── T6  [P] Migration: wallets + wallet_transactions
  └── T7  [P] Migration: bookings
```

### Phase 3 — Backend Auth & API (Sequential após Phase 2)
```
T4+T5+T6+T7 complete → T8 → T9 → T10
```

### Phase 4 — Models & Seeders (Parallel após T8)
```
T8 complete, então:
  ├── T11 [P] Models (User, Company, TravelPolicy)
  ├── T12 [P] Models (Wallet, WalletTransaction, Booking)
  ├── T13 [P] Seeder: Company + Users
  └── T14 [P] Seeder: TravelPolicies + Wallets
```

### Phase 5 — Vue SPA Fundação (Parallel após T9)
```
T9 complete, então:
  ├── T15 [P] Tailwind config (design tokens)
  ├── T16 [P] Vue Router setup + rotas protegidas
  └── T17 [P] Pinia auth store
```

### Phase 6 — Componentes Base (Parallel após Phase 5)
```
T15+T16+T17 complete, então:
  ├── T18 [P] BaseButton.vue
  ├── T19 [P] BaseCard.vue
  ├── T20 [P] BaseInput.vue
  └── T21 [P] BaseBadge.vue
```

### Phase 7 — Layout & Pages (Parallel após Phase 6)
```
T18+T19+T20+T21 complete, então:
  ├── T22 [P] TheHeader.vue
  ├── T23 [P] TheSidebar.vue (traveler)
  ├── T24 [P] TheSidebar.vue (approver)
  └── T25 [P] AppShell.vue
```

### Phase 8 — Pages (Parallel após T25)
```
T25 complete, então:
  ├── T26 [P] LoginPage.vue
  ├── T27 [P] Páginas placeholder: Traveler (3 pages)
  └── T28 [P] Páginas placeholder: Approver (2 pages)
```

### Phase 9 — Integração Final (Sequential)
```
T26+T27+T28 complete → T29 → T30
```

---

## Task Breakdown

### T1: Criar projeto Laravel + instalar dependências PHP
**What**: Inicializar projeto Laravel via `composer create-project` e instalar Laravel Sanctum
**Where**: `/` (raiz do projeto)
**Depends on**: None
**Requirement**: SCAF-01

**Done when**:
- [ ] `composer install` roda sem erros
- [ ] `php artisan` lista comandos
- [ ] `config/sanctum.php` existe

**Verify**: `php artisan --version` → retorna versão Laravel

---

### T2: Configurar Vite + Vue 3 + Tailwind CSS
**What**: Instalar e configurar Vue 3, Vite, Tailwind CSS e dependências frontend
**Where**: `package.json`, `vite.config.js`, `tailwind.config.js`, `postcss.config.js`
**Depends on**: T1
**Requirement**: SCAF-01

**Done when**:
- [ ] `npm install` roda sem erros
- [ ] `npm run dev` inicia servidor Vite com HMR
- [ ] Tailwind processa classes em `.vue` files
- [ ] `resources/js/app.js` monta Vue app no `#app`

**Verify**: `npm run build` → sem erros de compilação

---

### T3: Configurar Laravel Sanctum para SPA
**What**: Publicar e configurar Sanctum para autenticação SPA (cookie-based), adicionar middleware e CORS
**Where**: `config/sanctum.php`, `config/cors.php`, `app/Http/Kernel.php`, `routes/api.php`
**Depends on**: T2
**Requirement**: SCAF-02

**Done when**:
- [ ] `SANCTUM_STATEFUL_DOMAINS=localhost` no `.env`
- [ ] `api` middleware group inclui `EnsureFrontendRequestsAreStateful`
- [ ] CORS configurado para `localhost`
- [ ] Rota `POST /api/login` e `POST /api/logout` existem em `routes/api.php`

**Verify**: `php artisan route:list --name=sanctum` mostra rotas de auth

---

### T4: Migration — companies + users [P]
**What**: Criar migrations para `companies` e `users` (com campo `role`)
**Where**: `database/migrations/`
**Depends on**: T3
**Requirement**: SCAF-03

**Done when**:
- [ ] Migration `companies`: id, name, cnpj, timestamps
- [ ] Migration `users`: id, company_id(FK), name, email, password, role(enum: traveler|approver), department, position, avatar_url, timestamps
- [ ] `php artisan migrate` roda sem erros

---

### T5: Migration — travel_policies [P]
**What**: Criar migration para `travel_policies`
**Where**: `database/migrations/`
**Depends on**: T3
**Requirement**: SCAF-03

**Done when**:
- [ ] Colunas: id, company_id(FK), destination_city, destination_state, max_daily_hotel, max_daily_food, max_flight (decimals), timestamps
- [ ] Roda sem erros em `php artisan migrate`

---

### T6: Migration — wallets + wallet_transactions [P]
**What**: Criar migrations para `wallets` e `wallet_transactions`
**Where**: `database/migrations/`
**Depends on**: T3
**Requirement**: SCAF-03

**Done when**:
- [ ] `wallets`: id, user_id(FK), balance(decimal 10,2), timestamps
- [ ] `wallet_transactions`: id, wallet_id(FK), booking_id(FK nullable), type(enum: credit|debit|expiry), amount(decimal), description, expires_at(date), created_at
- [ ] Roda sem erros

---

### T7: Migration — bookings [P]
**What**: Criar migration para `bookings`
**Where**: `database/migrations/`
**Depends on**: T3
**Requirement**: SCAF-03

**Done when**:
- [ ] Colunas: id, user_id(FK), travel_policy_id(FK), modal(enum: hotel|flight|bus|car), destination_city, destination_state, provider_name, original_price, paid_price, savings_total, onhappy_coins_amount, company_savings (decimals), check_in, check_out (dates), status(enum: confirmed|cancelled), timestamps
- [ ] Roda sem erros

---

### T8: AuthController — login/logout API
**What**: Criar `AuthController` com métodos `login` e `logout` usando Sanctum
**Where**: `app/Http/Controllers/Api/AuthController.php`
**Depends on**: T4, T5, T6, T7
**Requirement**: SCAF-02

**Done when**:
- [ ] `POST /api/login`: valida credenciais, cria sessão Sanctum, retorna user com role
- [ ] `POST /api/logout`: invalida sessão
- [ ] `GET /api/user`: retorna usuário autenticado com role
- [ ] Erros 422 para credenciais inválidas

**Verify**: `curl -X POST localhost/api/login -d "email=...&password=..."` retorna JSON com user.role

---

### T9: Middleware CheckRole
**What**: Criar middleware `CheckRole` que rejeita acesso por role incorreta
**Where**: `app/Http/Middleware/CheckRole.php`, registrar em `Kernel.php`
**Depends on**: T8
**Requirement**: SCAF-02

**Done when**:
- [ ] Middleware aceita parâmetro `role` (traveler|approver)
- [ ] Retorna 403 JSON se role não confere
- [ ] Rotas em `api.php` usam `middleware(['auth:sanctum', 'role:traveler'])` e `role:approver`

---

### T10: Estrutura de rotas API agrupadas por role
**What**: Definir grupos de rotas em `routes/api.php` para traveler e approver
**Where**: `routes/api.php`
**Depends on**: T9
**Requirement**: SCAF-02

**Done when**:
- [ ] Grupo `/api/traveler/*` protegido com `auth:sanctum + role:traveler`
- [ ] Grupo `/api/approver/*` protegido com `auth:sanctum + role:approver`
- [ ] Rotas placeholder retornam `{"status": "ok"}` para confirmar proteção

---

### T11: Models — User, Company, TravelPolicy [P]
**What**: Criar Eloquent Models com relationships e fillable
**Where**: `app/Models/User.php`, `Company.php`, `TravelPolicy.php`
**Depends on**: T8
**Requirement**: SCAF-03

**Done when**:
- [ ] `User` belongsTo Company, hasOne Wallet, hasMany Bookings; cast de `role` para string
- [ ] `Company` hasMany Users, hasMany TravelPolicies
- [ ] `TravelPolicy` belongsTo Company; casts decimais

---

### T12: Models — Wallet, WalletTransaction, Booking [P]
**What**: Criar Eloquent Models com relationships
**Where**: `app/Models/Wallet.php`, `WalletTransaction.php`, `Booking.php`
**Depends on**: T8
**Requirement**: SCAF-03

**Done when**:
- [ ] `Wallet` belongsTo User, hasMany WalletTransactions
- [ ] `WalletTransaction` belongsTo Wallet; cast de `type`; cast `expires_at` para date
- [ ] `Booking` belongsTo User, belongsTo TravelPolicy; casts decimais e datas

---

### T13: Seeder — Company + Users (dados realistas) [P]
**What**: Criar seeders com 1 empresa, 5 viajantes e 2 aprovadores com dados brasileiros reais
**Where**: `database/seeders/CompanySeeder.php`, `UserSeeder.php`
**Depends on**: T11
**Requirement**: SCAF-03, SCAF-05

**Done when**:
- [ ] Empresa: "Construtora Meridian Ltda" (CNPJ fictício formatado)
- [ ] 5 viajantes: nomes brasileiros, cargos reais (Analista, Coordenador, Gerente, Consultor, Engenheiro), email padronizado
- [ ] 2 aprovadores: Gerente de Viagens + CFO
- [ ] Senha padrão: `password` (hasheada)
- [ ] `php artisan db:seed` roda idempotente (truncate antes de inserir)

---

### T14: Seeder — TravelPolicies + Wallets [P]
**What**: Criar seeders para políticas de teto por destino e carteiras iniciais
**Where**: `database/seeders/TravelPolicySeeder.php`, `WalletSeeder.php`
**Depends on**: T12
**Requirement**: SCAF-03, SCAF-05

**Done when**:
- [ ] Políticas para 4 destinos: São Paulo/SP (R$750 hotel), Curitiba/PR (R$500), Belo Horizonte/MG (R$550), Rio de Janeiro/RJ (R$700)
- [ ] Cada viajante tem 1 carteira com `balance = 0.00`
- [ ] Idempotente

---

### T15: tailwind.config.js com design tokens [P]
**What**: Configurar Tailwind com todos os tokens do design system (cores, fontes, espaçamento, radius, animações)
**Where**: `tailwind.config.js`
**Depends on**: T9
**Requirement**: SCAF-04

**Done when**:
- [ ] Cores: brand, reward, gold, surface, ink, destructive (conforme design.md)
- [ ] Fontes: Inter (sans), JetBrains Mono (mono)
- [ ] Border radius: btn(8px), card(12px), modal(16px)
- [ ] Animation: pulse-slow
- [ ] Google Fonts importado via `@import` no CSS base

---

### T16: Vue Router — setup com rotas protegidas [P]
**What**: Configurar Vue Router com navigation guard baseado em auth e role
**Where**: `resources/js/router/index.js`
**Depends on**: T9
**Requirement**: SCAF-02, SCAF-04

**Done when**:
- [ ] Rota `/login` pública
- [ ] Rotas `/traveler/*` requerem auth + role traveler
- [ ] Rotas `/approver/*` requerem auth + role approver
- [ ] Navigation guard redireciona não autenticados para `/login`
- [ ] Navigation guard redireciona role errada para sua área correta

---

### T17: Pinia — auth store [P]
**What**: Criar store de autenticação com estado do usuário, login, logout
**Where**: `resources/js/stores/auth.js`
**Depends on**: T9
**Requirement**: SCAF-02

**Done when**:
- [ ] Estado: `user`, `isAuthenticated`, `role`
- [ ] Action `login(email, password)` → chama `POST /api/login` via axios, salva user
- [ ] Action `logout()` → chama `POST /api/logout`, limpa estado
- [ ] Action `fetchUser()` → GET /api/user para restaurar sessão no reload
- [ ] Persiste role no localStorage para guard de rota

---

### T18: BaseButton.vue [P]
**What**: Componente de botão com variantes primary, secondary, ghost e estados hover/disabled/loading
**Where**: `resources/js/components/ui/BaseButton.vue`
**Depends on**: T15, T16, T17
**Requirement**: SCAF-04

**Done when**:
- [ ] Props: `variant` (primary|secondary|ghost), `loading` (bool), `disabled` (bool), `size` (sm|md|lg)
- [ ] Loading mostra spinner inline, desabilita interação
- [ ] Todos os estados visuais (hover, active, focus ring, disabled) implementados
- [ ] Cursor pointer; mínimo 44px de altura para touch

---

### T19: BaseCard.vue [P]
**What**: Componente de card com padding, border, radius e slot padrão
**Where**: `resources/js/components/ui/BaseCard.vue`
**Depends on**: T15, T16, T17
**Requirement**: SCAF-04

**Done when**:
- [ ] Surface-1 background, border `--border`, radius card(12px), padding 20px
- [ ] Props: `padding` (default|compact|none), `hoverable` (bool)
- [ ] Slot padrão

---

### T20: BaseInput.vue [P]
**What**: Componente de input com label visível, helper text, erro e estados
**Where**: `resources/js/components/ui/BaseInput.vue`
**Depends on**: T15, T16, T17
**Requirement**: SCAF-04

**Done when**:
- [ ] Props: `label`, `error`, `helper`, `type`, `disabled`
- [ ] Label sempre visível acima do input (nunca só placeholder)
- [ ] Erro aparece abaixo do input em `--destructive`
- [ ] Focus ring com `--brand`
- [ ] Background `--surface-2` (inset)

---

### T21: BaseBadge.vue [P]
**What**: Componente de badge/selo com variantes reward, gold, brand, neutral
**Where**: `resources/js/components/ui/BaseBadge.vue`
**Depends on**: T15, T16, T17
**Requirement**: SCAF-04

**Done when**:
- [ ] Props: `variant` (reward|gold|brand|neutral), `pulse` (bool)
- [ ] `pulse=true` aplica animação `pulse-slow` respeitando `prefers-reduced-motion`
- [ ] Contraste mínimo 4.5:1 em todas as variantes

---

### T22: TheHeader.vue [P]
**What**: Header fixo com nome do usuário, empresa, avatar e botão de logout
**Where**: `resources/js/components/layout/TheHeader.vue`
**Depends on**: T18, T19, T20, T21
**Requirement**: SCAF-04

**Done when**:
- [ ] Exibe nome do usuário e empresa (da auth store)
- [ ] Avatar circular com iniciais se sem foto
- [ ] Botão logout chama `auth.logout()` e redireciona para `/login`
- [ ] Height 64px, borda inferior `--border`

---

### T23: TheSidebar.vue — perfil viajante [P]
**What**: Sidebar com navegação do viajante: Buscar Viagem, Minha Carteira, Meu Histórico
**Where**: `resources/js/components/layout/TheSidebar.vue`
**Depends on**: T18, T19, T20, T21
**Requirement**: SCAF-04

**Done when**:
- [ ] Itens: ícone Lucide + label + RouterLink
- [ ] Active state: fundo `--brand-light`, borda esquerda 3px `--brand`, texto `--brand`
- [ ] Background `--surface-1`, borda direita `--border-emphasis`
- [ ] Logo Onhappy no topo
- [ ] Width 240px fixed

---

### T24: TheSidebar.vue — perfil aprovador [P]
**What**: Variante da sidebar com navegação do aprovador: Dashboard de Economia, Ranking
**Where**: `resources/js/components/layout/TheSidebar.vue` (condicional por prop role)
**Depends on**: T18, T19, T20, T21
**Requirement**: SCAF-04

**Done when**:
- [ ] Items condicionados pelo prop `role` recebido do AppShell
- [ ] Itens aprovador: Dashboard de Economia (BarChart2), Ranking de Economia (Trophy)
- [ ] Mesma estilização do T23

---

### T25: AppShell.vue — layout principal
**What**: Layout container que combina TheHeader + TheSidebar + content slot, roteado por perfil
**Where**: `resources/js/layouts/AppShell.vue`
**Depends on**: T22, T23, T24
**Requirement**: SCAF-04

**Done when**:
- [ ] Lê role da auth store e passa prop para TheSidebar
- [ ] Layout: sidebar fixed left 240px + header fixed top 64px + main content com offset correto
- [ ] Sidebar collapsível em mobile (drawer com overlay)
- [ ] Slot `default` renderiza o conteúdo da page

---

### T26: LoginPage.vue [P]
**What**: Página de login com form, validação, loading e redirect por role
**Where**: `resources/js/pages/LoginPage.vue`
**Depends on**: T25, T26 (AppShell)
**Depends on**: T17 (auth store)
**Requirement**: SCAF-02

**Done when**:
- [ ] Form com email + senha usando `BaseInput`
- [ ] Botão "Entrar" com loading state durante a request
- [ ] Erro inline: "Email ou senha incorretos" abaixo do form
- [ ] Sucesso: redirect para `/traveler/search` ou `/approver/dashboard` baseado na role
- [ ] Logo Onhappy centralizado acima do card de login
- [ ] Background `--surface-0`

---

### T27: Pages placeholder — Traveler (3 páginas) [P]
**What**: Criar páginas placeholder com heading e descrição para as 3 áreas do viajante
**Where**: `resources/js/pages/traveler/SearchPage.vue`, `WalletPage.vue`, `HistoryPage.vue`
**Depends on**: T25
**Requirement**: SCAF-04

**Done when**:
- [ ] Cada página usa `AppShell`, exibe título da seção e subtítulo "Em breve"
- [ ] SearchPage: "Buscar Viagem" com ícone Search
- [ ] WalletPage: "Minha Carteira" com ícone Wallet
- [ ] HistoryPage: "Meu Histórico" com ícone Clock
- [ ] Visual consistente com o design system

---

### T28: Pages placeholder — Approver (2 páginas) [P]
**What**: Criar páginas placeholder para as 2 áreas do aprovador
**Where**: `resources/js/pages/approver/DashboardPage.vue`, `RankingPage.vue`
**Depends on**: T25
**Requirement**: SCAF-04

**Done when**:
- [ ] DashboardPage: "Dashboard de Economia" com ícone BarChart2
- [ ] RankingPage: "Ranking de Economia" com ícone Trophy
- [ ] Visual consistente

---

### T29: DatabaseSeeder — orquestrar todos os seeders
**What**: Orquestrar a ordem correta dos seeders no `DatabaseSeeder.php`
**Where**: `database/seeders/DatabaseSeeder.php`
**Depends on**: T26, T27, T28 (todos os seeders criados)
**Requirement**: SCAF-03

**Done when**:
- [ ] Ordem: CompanySeeder → UserSeeder → TravelPolicySeeder → WalletSeeder
- [ ] `php artisan db:seed` roda completo sem erros
- [ ] Re-executar limpa e repopula (idempotente)

---

### T30: Smoke test de integração end-to-end
**What**: Verificar que o fluxo completo funciona: setup → seed → login → navegação
**Where**: Manual (verificação)
**Depends on**: T29
**Requirement**: SCAF-01, SCAF-02, SCAF-03, SCAF-04

**Done when**:
- [ ] `composer install && npm install` → sem erros
- [ ] `php artisan migrate:fresh --seed` → banco populado
- [ ] `npm run dev` + `php artisan serve` → SPA disponível em localhost
- [ ] Login com viajante → sidebar traveler visível, rotas approver bloqueadas
- [ ] Login com aprovador → sidebar approver visível, rotas traveler bloqueadas
- [ ] Logout → redirect para /login
- [ ] Navegação entre as 5 páginas sem reload completo (SPA)

---

## Parallel Execution Map

```
Phase 1 (Sequential):
  T1 → T2 → T3

Phase 2 (Parallel, após T3):
  ├── T4 [P]
  ├── T5 [P]
  ├── T6 [P]
  └── T7 [P]

Phase 3 (Sequential, após Phase 2):
  T8 → T9 → T10

Phase 4 (Parallel, após T8):
  ├── T11 [P]
  ├── T12 [P]
  ├── T13 [P]
  └── T14 [P]

Phase 5 (Parallel, após T9):
  ├── T15 [P]
  ├── T16 [P]
  └── T17 [P]

Phase 6 (Parallel, após T15+T16+T17):
  ├── T18 [P]
  ├── T19 [P]
  ├── T20 [P]
  └── T21 [P]

Phase 7 (Parallel, após T18+T19+T20+T21):
  ├── T22 [P]
  ├── T23 [P]
  ├── T24 [P]
  └── T25 (aguarda T22+T23+T24)

Phase 8 (Parallel, após T25):
  ├── T26 [P]
  ├── T27 [P]
  └── T28 [P]

Phase 9 (Sequential):
  T29 → T30
```

**Total: 30 tarefas | 8 fases | máximo paralelismo nas fases 2, 4, 5, 6, 7, 8**
