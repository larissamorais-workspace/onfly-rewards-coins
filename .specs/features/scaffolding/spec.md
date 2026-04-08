# Scaffolding do Projeto — Specification

## Problem Statement

O projeto Onfly Rewards precisa de uma base sólida antes de qualquer feature de negócio. Sem o setup adequado (autenticação, perfis, estrutura de banco, layout base), cada feature subsequente precisaria resolver problemas de infraestrutura, atrasando o hackathon.

## Goals

- [ ] Projeto Laravel + Vue 3 rodando localmente com hot reload
- [ ] Dois perfis funcionais (viajante e aprovador) com login e navegação separada
- [ ] Banco de dados com schema base + seeds de dados simulados realistas
- [ ] Layout visual que simula a identidade Onfly para apresentação do hackathon

## Out of Scope

| Feature | Reason |
|---------|--------|
| Autenticação OAuth/SSO | MVP usa auth simples — sem integração externa |
| Multi-tenancy real | Seed simula uma empresa apenas |
| Testes automatizados | Hackathon prioriza velocidade de entrega |
| CI/CD | Projeto local, sem deploy automatizado |
| i18n | Apenas português brasileiro |

---

## User Stories

### P1: Setup do Projeto Laravel + Vue 3 ⭐ MVP

**User Story**: Como desenvolvedor, quero um projeto Laravel com Vue 3 configurado para que eu possa desenvolver features de frontend e backend de forma integrada.

**Why P1**: Sem o projeto base, nenhuma feature pode ser construída.

**Acceptance Criteria**:

1. WHEN o dev executa `composer install && npm install && npm run dev` THEN o sistema SHALL iniciar o servidor com hot reload funcionando
2. WHEN o dev acessa `localhost` THEN o sistema SHALL exibir a página inicial do Onfly Rewards
3. WHEN o dev cria um componente Vue THEN o sistema SHALL refletir a mudança automaticamente no browser

**Independent Test**: Abrir o terminal, rodar os comandos de setup e ver a página inicial renderizada.

---

### P1: Autenticação com Perfis (Viajante/Aprovador) ⭐ MVP

**User Story**: Como usuário, quero fazer login e ser direcionado à área correta do sistema (viajante ou aprovador) para acessar as funcionalidades do meu perfil.

**Why P1**: Todo o produto depende de dois perfis distintos com visões diferentes.

**Acceptance Criteria**:

1. WHEN um usuário acessa o sistema sem autenticação THEN o sistema SHALL redirecionar para a tela de login
2. WHEN um viajante faz login THEN o sistema SHALL exibir o menu do viajante (busca, carteira, histórico)
3. WHEN um aprovador faz login THEN o sistema SHALL exibir o menu do aprovador (dashboard de economia, ranking)
4. WHEN um usuário logado tenta acessar área do outro perfil THEN o sistema SHALL redirecionar para sua área correta
5. WHEN um usuário clica em "Sair" THEN o sistema SHALL encerrar a sessão e redirecionar ao login

**Independent Test**: Fazer login com credenciais de viajante e ver o menu correto; repetir com aprovador e ver menu diferente.

---

### P1: Schema de Banco de Dados Base ⭐ MVP

**User Story**: Como desenvolvedor, quero o banco de dados estruturado com as tabelas fundamentais para que as features de negócio tenham onde persistir dados.

**Why P1**: Carteira, reservas e economia dependem de tabelas bem definidas desde o início.

**Acceptance Criteria**:

1. WHEN o dev executa `php artisan migrate` THEN o sistema SHALL criar todas as tabelas: users, companies, travel_policies, wallets, wallet_transactions, bookings
2. WHEN o dev executa `php artisan db:seed` THEN o sistema SHALL popular o banco com dados simulados: 1 empresa, 5 viajantes, 2 aprovadores, políticas de teto por destino
3. WHEN o dev consulta a tabela `wallets` THEN cada viajante SHALL ter uma carteira com saldo inicial zero
4. WHEN o dev consulta `travel_policies` THEN SHALL existir tetos orçamentários para pelo menos 3 destinos diferentes

**Independent Test**: Rodar migrations + seeds e verificar dados no banco via `php artisan tinker`.

---

### P1: Layout Base e Navegação ⭐ MVP

**User Story**: Como usuário, quero uma interface visual limpa e profissional que simule a experiência Onfly para que a apresentação do hackathon seja impactante.

**Why P1**: Hackathon é visual — a primeira impressão define a percepção do produto.

**Acceptance Criteria**:

1. WHEN o viajante está logado THEN o sistema SHALL exibir sidebar com: Buscar Viagem, Minha Carteira, Meu Histórico
2. WHEN o aprovador está logado THEN o sistema SHALL exibir sidebar com: Dashboard de Economia, Ranking de Economia
3. WHEN o usuário navega entre páginas THEN o sistema SHALL manter o layout (sidebar + header) sem reload completo (SPA)
4. WHEN o sistema é visualizado em tela de apresentação (1920x1080) THEN o layout SHALL estar visualmente alinhado e sem quebras

**Independent Test**: Navegar por todas as rotas como viajante e como aprovador, verificando que sidebar, header e conteúdo renderizam corretamente.

---

### P2: Dados Seed Realistas

**User Story**: Como apresentador no hackathon, quero dados simulados que pareçam reais para que a demonstração seja convincente.

**Why P2**: Dados genéricos ("User 1", "Hotel A") prejudicam a apresentação, mas não bloqueiam o desenvolvimento.

**Acceptance Criteria**:

1. WHEN o seed é executado THEN os viajantes SHALL ter nomes brasileiros realistas e cargos corporativos
2. WHEN o seed é executado THEN os destinos SHALL incluir polos corporativos reais (Faria Lima/SP, Savassi/BH, Batel/Curitiba)
3. WHEN o seed é executado THEN as políticas de teto SHALL refletir valores de mercado realistas (ex: R$ 400-800/diária)

**Independent Test**: Visualizar lista de viajantes e políticas no sistema e avaliar se parecem dados reais.

---

## Edge Cases

- WHEN o banco de dados não existe THEN `php artisan migrate` SHALL criar o banco e rodar as migrations sem erro
- WHEN o seed é executado duas vezes THEN o sistema SHALL limpar dados anteriores antes de re-popular (idempotente)
- WHEN um usuário tenta acessar rota inexistente THEN o sistema SHALL exibir página 404 estilizada

---

## Requirement Traceability

| Requirement ID | Story | Phase | Status |
|----------------|-------|-------|--------|
| SCAF-01 | P1: Setup Projeto | Design | Pending |
| SCAF-02 | P1: Autenticação com Perfis | Design | Pending |
| SCAF-03 | P1: Schema de Banco | Design | Pending |
| SCAF-04 | P1: Layout Base e Navegação | Design | Pending |
| SCAF-05 | P2: Dados Seed Realistas | - | Pending |

**Coverage:** 5 total, 0 mapped to tasks, 5 unmapped ⚠️

---

## Success Criteria

- [ ] `composer install && npm install && php artisan migrate --seed && npm run dev` funciona sem erros
- [ ] Login com viajante mostra menu completo do viajante
- [ ] Login com aprovador mostra menu completo do aprovador
- [ ] Navegação SPA sem reloads de página
- [ ] Visual profissional adequado para apresentação em hackathon
