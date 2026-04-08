# Dashboard do Aprovador & Ranking — Specification

## Problem Statement

O aprovador (CFO/gestor) não tem visibilidade sobre a economia gerada pela equipe. Sem essa informação, o modelo "Win-Win" é invisível para quem controla o orçamento — justamente quem precisa ver o ROI do Onfly Rewards para renovar a assinatura e virar defensor interno.

## Goals

- [ ] Aprovador visualiza economia total gerada pela equipe em painel de destaque
- [ ] Ranking mostra os viajantes que mais economizaram com seus onhappy coins e economia para a empresa
- [ ] Dados simulados realistas (bookings com histórico) para demo impactante no hackathon

## Out of Scope

| Feature | Reason |
|---------|--------|
| Filtros por período | Hackathon — dados agregados suficientes |
| Exportação CSV | Fora do escopo MVP |
| Comparativo entre meses | Complexidade desnecessária |
| Gestão de políticas pelo aprovador | Feature futura |

---

## User Stories

### P1: Dashboard de Economia ⭐ MVP

**User Story**: Como aprovador, quero ver um painel com a economia total gerada pela minha equipe para demonstrar o ROI do Onfly Rewards para a diretoria.

**Acceptance Criteria**:

1. WHEN o aprovador acessa DashboardPage THEN o sistema SHALL exibir: economia total da empresa, onhappy coins totais distribuídos, número de reservas com economia, número de viajantes ativos
2. WHEN os dados são exibidos THEN os valores monetários SHALL estar em destaque com fonte monoespaciada e cor brand/reward
3. WHEN existem bookings THEN o sistema SHALL exibir breakdown por modal (hotel, aéreo, ônibus, carro) com economia de cada um
4. WHEN a página carrega THEN o sistema SHALL buscar dados de `GET /api/approver/dashboard`

**Independent Test**: Login como aprovador → Dashboard exibe métricas com valores > 0 (dados seed).

---

### P1: Ranking de Economia ⭐ MVP

**User Story**: Como aprovador, quero ver quais viajantes mais economizaram para reconhecer e engajar os "campeões de eficiência".

**Acceptance Criteria**:

1. WHEN o aprovador acessa RankingPage THEN o sistema SHALL exibir leaderboard com os viajantes ordenados por economia total gerada
2. WHEN o ranking é exibido THEN cada entrada SHALL mostrar: posição, avatar com iniciais, nome, cargo, total economizado para empresa, onhappy coins ganhos, número de reservas
3. WHEN o viajante é top 1 THEN o sistema SHALL exibir destaque dourado (coroa/troféu)
4. WHEN o viajante é top 2 ou 3 THEN o sistema SHALL exibir destaque prateado/bronze
5. WHEN não há bookings THEN o sistema SHALL exibir empty state "Nenhuma reserva econômica registrada ainda"

**Independent Test**: Ranking exibe viajantes com dados de economia após seed de bookings simulados.

---

### P1: Seed de Bookings Simulados ⭐ MVP

**User Story**: Como sistema, preciso de bookings históricos simulados para que o dashboard e ranking do aprovador tenham dados reais para demo.

**Acceptance Criteria**:

1. WHEN o seed é executado THEN o sistema SHALL criar 15-20 bookings distribuídos entre os 5 viajantes
2. WHEN os bookings são criados THEN SHALL incluir reservas de diferentes modais (hotel, aéreo) com preços mistos (alguns com onhappy coins, alguns sem)
3. WHEN um booking tem onhappy coins THEN o sistema SHALL criar o WalletTransaction e atualizar o Wallet.balance correspondente
4. WHEN o seed é re-executado THEN o sistema SHALL truncar bookings e wallet_transactions antes de recriar (idempotente)

**Independent Test**: `php artisan db:seed --class=BookingSeeder` → dashboard mostra valores > 0.

---

### P1: Backend — Endpoints do Aprovador

**Acceptance Criteria**:

1. WHEN `GET /api/approver/dashboard` THEN o sistema SHALL retornar:
   - `total_company_savings` — soma de company_savings de todos os bookings da empresa
   - `total_onhappy_coins_distributed` — soma de onhappy_coins_amount distribuídos
   - `total_bookings_with_savings` — contagem de bookings com savings_total > 0
   - `active_travelers` — contagem de viajantes com pelo menos 1 booking
   - `by_modal` — breakdown por modal: `{ hotel: { savings, count }, flight: {...}, ... }`

2. WHEN `GET /api/approver/ranking` THEN o sistema SHALL retornar array de viajantes ordenados por `company_savings_generated` DESC:
   - `user_id`, `name`, `position`, `department`
   - `total_company_savings` — quanto economizou para a empresa
   - `total_onhappy_coins_earned` — quantos onhappy coins ganhou
   - `total_bookings` — número de reservas

**Independent Test**: GET /api/approver/dashboard retorna JSON com valores numéricos > 0 após seed.

---

## Requirement Traceability

| ID | Story | Status |
|----|-------|--------|
| APPR-01 | P1: Dashboard de Economia | Pending |
| APPR-02 | P1: Ranking de Economia | Pending |
| APPR-03 | P1: Seed de Bookings | Pending |
| APPR-04 | P1: Backend Endpoints | Pending |

## Success Criteria

- [ ] Dashboard exibe economia total > R$ 1.000 (dados seed convincentes)
- [ ] Ranking lista 5 viajantes com valores distintos
- [ ] Visual impactante: top 3 com destaque dourado/prateado/bronze
- [ ] Fluxo demo: login aprovador → dashboard → ranking (< 30 segundos)
