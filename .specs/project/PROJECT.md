# Onfly Rewards

**Vision:** Transformar o módulo Onhappy em um motor de economia comportamental gamificada que recompensa colaboradores com onhappy coins por escolhas de viagem econômicas, gerando um ciclo "Win-Win" entre empresa e viajante.
**For:** Colaboradores corporativos (viajantes) e gestores/aprovadores de viagem (CFOs/RH) de empresas clientes da Onfly.
**Solves:** Eliminar o comportamento de "gastar até o teto" em viagens corporativas, transformando o viajante de "gastador regulado" em "caçador de eficiências".

## Goals

- Demonstrar o fluxo completo de gamificação em uma apresentação de hackathon funcional e visualmente impactante
- Exibir economia real simulada: busca → selo de onhappy coins → split monetário → créditos na carteira
- Apresentar duas perspectivas distintas: aprovador (dashboard de economia) e viajante (carteira de onhappy coins)
- Incluir ranking de viajantes que mais economizaram como prova de engajamento

## Tech Stack

**Core:**

- Frontend: Vue 3
- Backend: PHP Laravel
- Database: MySQL

**Key dependencies:**

- APIs abertas de pesquisa de modais de viagem (hotel, aéreo, ônibus, automóvel) — simuladas ou integradas
- Sistema de carteira digital com saldo e histórico de transações
- Motor de cálculo de split (divisão da economia entre colaborador e empresa)

## Scope

**v1 (MVP Hackathon) includes:**

- Fluxo de busca de viagem do zero (hotel, aéreo, ônibus, automóvel) com selos de onhappy coins
- Motor de split monetário (50/50): metade → onhappy coins para o viajante, metade → economia para a empresa
- Carteira digital do viajante: saldo em onhappy coins, histórico (sem validade)
- Menu do aprovador: dashboard com economia total gerada pela equipe
- Menu do viajante: onhappy coins ganhos, saldo disponível, histórico de reservas econômicas
- Ranking/relatório dos viajantes que mais economizaram (economia para empresa + onhappy coins ganhos)
- APIs abertas e seguras para pesquisa de modais de viagem
- Autocomplete de cidade com estado já selecionado no formulário de busca

**Explicitly out of scope:**

- Transferência de saldo entre colaboradores (intransferível)
- Resgate em dinheiro (cash-out)
- Integração real com sistemas de pagamento/financeiro da Onfly
- Gamificação avançada (badges, níveis, conquistas) — apenas ranking simples
- Upsell B2B e métricas de LTV/Churn (acompanhamento futuro)
- Validade/expiração de onhappy coins (créditos são permanentes)

## Constraints

- Timeline: O mais rápido possível para apresentação em hackathon
- Technical: Projeto standalone — simula a interface Onfly sem dependências externas reais
- Resources: Desenvolvedor solo
