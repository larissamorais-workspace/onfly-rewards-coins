# Roadmap

**Current Milestone:** M1 — Fundação & Fluxo Core
**Status:** Planning

---

## M1 — Fundação & Fluxo Core

**Goal:** Estrutura do projeto + fluxo de busca com selos de economia funcionando end-to-end
**Target:** Primeira apresentação funcional do hackathon

### Features

**Scaffolding do Projeto** — PLANNED

- Setup Laravel + Vue 3 (SPA ou Inertia)
- Estrutura de banco de dados (migrations)
- Autenticação básica com perfis (viajante / aprovador)
- Seed de dados simulados (empresas, colaboradores, políticas de teto)

**Busca de Viagem com Selos de Onhappy Coins** — PLANNED

- Tela de busca unificada (hotel, aéreo, ônibus, automóvel) com autocomplete de cidade por estado
- Motor de comparação: preço da opção vs. teto orçamentário da política
- Exibição do selo verde "✦ Ganhe X onhappy coins"
- Cálculo do split (50% viajante em onhappy coins / 50% empresa)
- APIs de pesquisa (simuladas ou abertas) para cada modal

---

## M2 — Carteira Digital & Reserva

**Goal:** Viajante consegue reservar, receber créditos e visualizar sua carteira
**Target:** Fluxo completo de reserva → crédito

### Features

**Fluxo de Reserva com Onhappy Coins** — PLANNED

- Seleção de opção econômica → confirmação da reserva
- Processamento do split monetário no backend
- Crédito automático de onhappy coins na carteira do viajante
- Registro da economia para a empresa

**Carteira Digital do Viajante** — PLANNED

- Tela de saldo atual em onhappy coins (sem validade)
- Histórico de créditos recebidos (data, viagem, valor em onhappy coins)
- Saldo bloqueado (intransferível, sem cash-out)

---

## M3 — Dashboard do Aprovador & Ranking

**Goal:** Gestores visualizam economia e ranking de viajantes engajados
**Target:** Apresentação completa do hackathon

### Features

**Dashboard do Aprovador** — PLANNED

- Economia total gerada pela equipe (período selecionável)
- Economia por colaborador
- Gráfico de tendência de economia ao longo do tempo
- Comparativo: gasto real vs. teto orçamentário

**Ranking de Economia** — PLANNED

- Tabela/leaderboard dos viajantes que mais economizaram
- Métricas: total economizado para empresa + total de onhappy coins ganhos pelo viajante
- Filtros por período e departamento

---

## Future Considerations

- Integração real com APIs de fornecedores (Amadeus, Booking, etc.)
- Integração com sistema financeiro da Onfly
- Gamificação avançada (badges, níveis, conquistas)
- Métricas de negócio: impacto no LTV, churn, volume transacional de lazer
- Uso dos créditos para reservas de lazer dentro do Onhappy
- Notificações push de créditos recebidos e próximos a expirar
