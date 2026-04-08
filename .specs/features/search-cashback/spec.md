# Busca de Viagem com Selos de Onhappy Coins — Specification

## Problem Statement

O viajante corporativo não tem incentivo para escolher opções abaixo do teto orçamentário. O sistema precisa exibir, no momento da busca, quais opções geram onhappy coins para o viajante — tornando a escolha econômica visualmente atraente e financeiramente recompensadora.

## Goals

- [ ] Viajante realiza busca de viagem por modal (hotel, aéreo, ônibus, automóvel), destino (com autocomplete de cidade por estado) e datas
- [ ] Resultados exibem o selo de onhappy coins animado nas opções que economizam em relação ao teto da política
- [ ] Valor dos onhappy coins (50% da economia) é exibido em destaque no selo
- [ ] Viajante confirma reserva e os onhappy coins são creditados na carteira
- [ ] Toda a experiência é simulada com dados mockados (sem API real)

## Out of Scope

| Feature | Reason |
|---------|--------|
| API real de fornecedores (Amadeus, Booking) | Hackathon — dados simulados suficientes |
| Pagamento real | Crédito é apenas na carteira interna |
| Multi-destino / viagem com escalas | MVP é origem → destino direto |
| Filtros avançados (estrelas, amenidades) | Complexidade desnecessária para hackathon |
| Comparação de múltiplos destinos | Fora do escopo atual |

---

## User Stories

### P1: Formulário de Busca ⭐ MVP

**User Story**: Como viajante, quero preencher um formulário de busca com modal, destino e datas para encontrar opções de viagem.

**Why P1**: Ponto de entrada de toda a funcionalidade.

**Acceptance Criteria**:

1. WHEN o viajante acessa a SearchPage THEN o sistema SHALL exibir formulário com: modal (hotel/aéreo/ônibus/automóvel), estado (select), cidade de destino (autocomplete filtrado pelo estado selecionado), data de ida e data de volta (opcional)
2. WHEN o viajante seleciona um estado THEN o sistema SHALL habilitar o campo de cidade com autocomplete mostrando apenas cidades do estado selecionado
3. WHEN o viajante digita no campo cidade THEN o sistema SHALL filtrar e sugerir cidades conforme a digitação
4. WHEN o viajante seleciona "Hotel" THEN o sistema SHALL exibir campo de datas check-in/check-out
5. WHEN o viajante seleciona "Aéreo" ou "Ônibus" THEN o sistema SHALL exibir data de ida e opção de volta
6. WHEN o viajante seleciona "Automóvel" THEN o sistema SHALL exibir datas de retirada e devolução
7. WHEN o viajante submete o formulário sem preencher campos obrigatórios THEN o sistema SHALL exibir mensagem de erro inline
8. WHEN o formulário é válido e submetido THEN o sistema SHALL exibir os resultados da busca

**Independent Test**: Selecionar estado "PR", digitar "Curi" no campo cidade, ver "Curitiba" no autocomplete, selecionar modal "Hotel" e ver resultados.

---

### P1: Lista de Resultados com Selos de Onhappy Coins ⭐ MVP

**User Story**: Como viajante, quero ver as opções de hospedagem/transporte com destaque visual nas opções que geram onhappy coins, para escolher conscientemente a opção mais econômica.

**Why P1**: É o coração do produto — o momento de decisão gamificada.

**Acceptance Criteria**:

1. WHEN os resultados são exibidos THEN o sistema SHALL mostrar mínimo 4 opções por busca
2. WHEN uma opção tem preço abaixo do teto da política de viagem THEN o sistema SHALL exibir o selo verde pulsante "✦ Ganhe X onhappy coins"
3. WHEN uma opção tem preço igual ou acima do teto THEN o sistema SHALL exibir a opção normalmente sem selo
4. WHEN o selo é exibido THEN o sistema SHALL calcular onhappy_coins = (teto - preço_opção) * 0.5 e exibir esse valor
5. WHEN o viajante passa o mouse sobre uma opção com selo THEN o sistema SHALL exibir breakdown: "Economia total: R$ X | Seus onhappy coins: Y | Empresa economiza: R$ Z"
6. WHEN não existe política de viagem para o destino buscado THEN o sistema SHALL exibir resultados sem selos e mensagem "Política não configurada para este destino"

**Independent Test**: Buscar "São Paulo" hotel — opções abaixo de R$ 750 devem ter selo com onhappy coins calculado corretamente.

---

### P1: Confirmação de Reserva com Onhappy Coins ⭐ MVP

**User Story**: Como viajante, quero confirmar a reserva de uma opção econômica e receber imediatamente onhappy coins na minha carteira.

**Why P1**: Fecha o loop de recompensa — sem isso o produto não tem valor demonstrável.

**Acceptance Criteria**:

1. WHEN o viajante clica em "Reservar" em uma opção THEN o sistema SHALL exibir modal de confirmação com resumo da reserva e onhappy coins a receber
2. WHEN o viajante confirma a reserva THEN o sistema SHALL criar o booking via API `POST /api/traveler/bookings`
3. WHEN a reserva é criada com economia THEN o sistema SHALL creditar os onhappy coins na carteira via `POST /api/traveler/bookings` (o backend processa o split)
4. WHEN a reserva é confirmada THEN o sistema SHALL exibir toast de sucesso "🎉 Você ganha onhappy coins na sua carteira!"
5. WHEN o viajante confirma THEN o sistema SHALL redirecionar para WalletPage mostrando o novo saldo

**Independent Test**: Reservar hotel com onhappy coins → ver toast de sucesso → ir para carteira e ver saldo atualizado.

---

### P1: Backend — Motor de Resultados Simulados ⭐ MVP

**User Story**: Como sistema, preciso gerar resultados de busca simulados e realistas para cada modal e destino, incluindo mix de opções acima e abaixo do teto.

**Why P1**: Sem dados, não há demonstração. A simulação precisa ser convincente.

**Acceptance Criteria**:

1. WHEN `GET /api/traveler/search?modal=hotel&destination_city=Curitiba&destination_state=PR&check_in=...` THEN o sistema SHALL retornar 4-6 opções com nomes de hotéis/cias reais brasileiras
2. WHEN o destino tem política configurada THEN o sistema SHALL incluir obrigatoriamente pelo menos 2 opções abaixo do teto e 2 acima
3. WHEN a resposta é retornada THEN cada opção SHALL conter: id, provider_name, price, original_price(=price), modal, address, rating(1-5), amenities[], onhappy_coins_amount, savings_total, has_onhappy_coins(bool)
4. WHEN o modal é "hotel" THEN os nomes SHALL ser hotéis conhecidos (ex: Ibis, Mercure, Bristol, Slaviero, Novotel)
5. WHEN o modal é "aéreo" THEN as opções SHALL incluir companhias (LATAM, Gol, Azul) com horários simulados
6. WHEN o modal é "ônibus" THEN as opções SHALL incluir empresas (Cometa, Itapemirim, Catarinense)
7. WHEN o modal é "automóvel" THEN as opções SHALL incluir locadoras (Localiza, Movida, Unidas)

**Independent Test**: GET /api/traveler/search?modal=hotel&destination_city=São Paulo&destination_state=SP retorna JSON com pelo menos 4 hotéis, mix acima/abaixo de R$750.

---

### P1: Backend — Endpoint de Reserva com Split ⭐ MVP

**User Story**: Como sistema, preciso processar a reserva, calcular o split financeiro e creditar os onhappy coins na carteira do viajante.

**Why P1**: É a operação core de negócio da tese.

**Acceptance Criteria**:

1. WHEN `POST /api/traveler/bookings` com dados da reserva THEN o sistema SHALL criar registro em `bookings`
2. WHEN paid_price < teto da política THEN o sistema SHALL calcular savings_total = teto - paid_price, onhappy_coins_amount = savings_total * 0.5, company_savings = savings_total * 0.5
3. WHEN os onhappy coins são calculados THEN o sistema SHALL criar `wallet_transaction` com type=credit, amount=onhappy_coins_amount (sem expires_at — créditos permanentes)
4. WHEN a transação é criada THEN o sistema SHALL incrementar `wallet.balance` do viajante com o onhappy_coins_amount
5. WHEN paid_price >= teto THEN o sistema SHALL criar booking com savings_total=0, onhappy_coins_amount=0 (sem crédito)
6. WHEN a reserva é criada THEN o sistema SHALL retornar: `{booking, onhappy_coins_earned, new_wallet_balance}`

**Independent Test**: POST com hotel R$320 em Curitiba (teto R$500) → resposta deve ter onhappy_coins_earned=90.00, new_wallet_balance=90.00.

---

### P2: Ordenação e Destaque Visual

**User Story**: Como viajante, quero que as opções com onhappy coins apareçam em destaque e as melhores economias no topo.

**Why P2**: Melhora a UX mas não bloqueia o fluxo core.

**Acceptance Criteria**:

1. WHEN os resultados são exibidos THEN o sistema SHALL ordenar: primeiro opções com onhappy coins (maior valor no topo), depois opções sem onhappy coins (menor preço no topo)
2. WHEN uma opção tem onhappy coins > 100 THEN o sistema SHALL aplicar borda verde mais intensa no card

**Independent Test**: Opção com 200 onhappy coins aparece antes da opção com 50 onhappy coins.

---

### P2: WalletPage — Saldo e Histórico de Transações

**User Story**: Como viajante, quero ver meu saldo atual em onhappy coins e o histórico de créditos recebidos na carteira.

**Why P2**: Fecha o loop visual de recompensa. Sem wallet, não há onde "chegar" após a reserva.

**Acceptance Criteria**:

1. WHEN o viajante acessa WalletPage THEN o sistema SHALL exibir saldo atual em onhappy coins em destaque (fonte mono, cor reward)
2. WHEN existem transações THEN o sistema SHALL listar: data, descrição (ex: "Reserva Hotel Ibis Curitiba"), valor em onhappy coins
3. WHEN não há transações THEN o sistema SHALL exibir empty state "Faça sua primeira reserva econômica para ganhar onhappy coins"

**Independent Test**: Após reservar com onhappy coins, acessar WalletPage e ver o crédito listado com data e valor corretos.

---

## Edge Cases

- WHEN destino não tem política configurada THEN resultado sem selos, mensagem informativa
- WHEN todos os resultados estão acima do teto THEN nenhum selo exibido, mensagem "Não encontramos opções dentro da política para este destino"
- WHEN viajante já tem reserva confirmada e tenta reservar novamente THEN sistema permite (sem restrição — é demo)
- WHEN o cálculo de onhappy coins resulta em valor < 1 THEN não exibir selo (economia insignificante)

---

## Requirement Traceability

| Requirement ID | Story | Phase | Status |
|----------------|-------|-------|--------|
| SRCH-01 | P1: Formulário de Busca | Design | Pending |
| SRCH-02 | P1: Lista com Selos | Design | Pending |
| SRCH-03 | P1: Confirmação de Reserva | Design | Pending |
| SRCH-04 | P1: Backend Resultados Simulados | Design | Pending |
| SRCH-05 | P1: Backend Reserva + Split | Design | Pending |
| SRCH-06 | P2: Ordenação e Destaque | - | Pending |
| SRCH-07 | P2: WalletPage Saldo+Histórico | - | Pending |

**Coverage:** 7 total, 0 mapped to tasks, 7 unmapped ⚠️

---

## Success Criteria

- [ ] Busca retorna resultados em < 500ms (dados mockados)
- [ ] Selo de onhappy coins exibe valor correto (teto - preço) * 0.5
- [ ] Reserva credita onhappy coins e saldo da carteira reflete imediatamente
- [ ] Fluxo completo demonstrável em 2 minutos: login → busca → reserva → carteira
- [ ] Visual impactante o suficiente para hackathon: selo verde pulsante, cores do design system
- [ ] Autocomplete de cidade funciona corretamente após seleção de estado
