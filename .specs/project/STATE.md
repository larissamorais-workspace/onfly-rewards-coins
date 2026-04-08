# Project State

## Decisions

- **D1:** Projeto standalone para hackathon — simula interface Onfly sem dependências reais
- **D2:** Split monetário fixo 50/50 (viajante/empresa) no MVP
- **D3:** Créditos (onhappy coins) intransferíveis, sem cash-out, sem validade
- **D4:** APIs de busca de modais serão simuladas ou usarão APIs abertas gratuitas
- **D5:** Dois perfis de usuário: viajante (recebe onhappy coins) e aprovador (vê economia)
- **D6:** Laravel 13 já vem com Tailwind v4 via @tailwindcss/vite. Design tokens em T15 usarão sintaxe `@theme {}` no CSS (não tailwind.config.js v3)
- **D7:** Nome do sistema: "Onfly Rewards" (não "Onhappy Wallet")
- **D8:** Moeda do sistema: "onhappy coins" (não "cashback"). Toda referência à recompensa usa "onhappy coins"
- **D9:** Onhappy coins NÃO têm validade — créditos são permanentes (removida expiração de 6 meses)
- **D10:** Mensagem ao creditar: "você ganha onhappy coins na sua carteira"
- **D11:** Formulário de busca: autocomplete de cidade com estado já selecionado (select estado → autocomplete cidade)

## Blockers

- Nenhum identificado

## Lessons

- (nenhuma ainda)

## Todos

- [x] Definir se usará Inertia.js ou SPA pura (Vue Router + API REST) → **SPA + API REST**
- [ ] Pesquisar APIs abertas/gratuitas para dados de hotel, aéreo, ônibus
- [ ] Definir design system / componentes visuais para o hackathon

## Deferred Ideas

- Notificações push quando créditos estão próximos de expirar
- Modo "comparador" que mostra lado a lado: opção cara vs. opção econômica + onhappy coins
- QR code para apresentação do saldo na hora de usar créditos em lazer

## Preferences

- Desenvolvedor solo — manter complexidade baixa e foco em apresentação visual
