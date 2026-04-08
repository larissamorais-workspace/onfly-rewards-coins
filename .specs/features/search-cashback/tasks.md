# Busca com Selos de Onhappy Coins — Tasks

**Design**: `.specs/features/search-cashback/design.md`
**Status**: Approved

---

## Execution Plan

```
Phase 1 (Sequential):
  T1 → T2 → T3

Phase 2 (Parallel, após T1):
  ├── T4 [P]  MockDataGenerator service
  └── T5 [P]  SearchController

Phase 3 (Sequential, após T2+T5):
  T6 → T7

Phase 4 (Parallel, após T3):
  ├── T8  [P]  useSearchStore (Pinia)
  ├── T9  [P]  SearchForm.vue
  └── T10 [P]  OnhappyCoinsBadge.vue

Phase 5 (Parallel, após T8+T9+T10):
  ├── T11 [P]  ResultCard.vue
  └── T12 [P]  BookingModal.vue

Phase 6 (Sequential, após T11+T12):
  T13 (SearchPage.vue completa)

Phase 7 (Parallel, após T6):
  ├── T14 [P]  WalletBalance.vue + TransactionList.vue
  └── T15 [P]  WalletPage.vue
```

---

## Task Breakdown

### T1: Rotas API — traveler (search, bookings, wallet)
**What**: Adicionar rotas em `routes/api.php` para SearchController, BookingController e WalletController
**Where**: `routes/api.php`
**Depends on**: None

```php
Route::middleware(['auth:sanctum', 'role:traveler'])->prefix('traveler')->group(function () {
    Route::get('/search', [SearchController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/wallet', [WalletController::class, 'show']);
    // existentes ficam
});
```

**Done when**:
- [ ] 3 novas rotas registradas
- [ ] `php artisan route:list` mostra GET traveler/search, POST traveler/bookings, GET traveler/wallet

---

### T2: BookingController — criar reserva + split
**What**: Controller que processa reserva, calcula split e credita carteira
**Where**: `app/Http/Controllers/Api/Traveler/BookingController.php`
**Depends on**: T1

**Logic**:
```php
public function store(Request $request) {
    $data = $request->validate([...]);
    $user = $request->user()->load('wallet');
    $policy = TravelPolicy::find($data['travel_policy_id']);

    $teto = match($data['modal']) {
        'hotel' => $policy?->max_daily_hotel,
        'flight' => $policy?->max_flight,
        default => null,
    };

    $savings = $teto ? max(0, $teto - $data['paid_price']) : 0;
    $onhappyCoins = round($savings * 0.5, 2);
    $companySavings = round($savings * 0.5, 2);

    $booking = Booking::create([...$data, 'user_id' => $user->id,
        'savings_total' => $savings, 'onhappy_coins_amount' => $onhappyCoins,
        'company_savings' => $companySavings, 'status' => 'confirmed']);

    if ($onhappyCoins > 0) {
        WalletTransaction::create([
            'wallet_id' => $user->wallet->id,
            'booking_id' => $booking->id,
            'type' => 'credit',
            'amount' => $onhappyCoins,
            'description' => "Onhappy Coins: {$booking->provider_name}",
        ]);
        $user->wallet->increment('balance', $onhappyCoins);
    }

    return response()->json([
        'booking' => $booking,
        'onhappy_coins_earned' => $onhappyCoins,
        'new_wallet_balance' => $user->wallet->fresh()->balance,
    ]);
}
```

**Done when**:
- [ ] Controller criado
- [ ] Booking criado no banco
- [ ] WalletTransaction criada se onhappy coins > 0
- [ ] Wallet.balance incrementado
- [ ] Retorna `onhappy_coins_earned` e `new_wallet_balance`

---

### T3: WalletController — saldo + histórico
**What**: Controller que retorna saldo e transações da carteira do usuário logado
**Where**: `app/Http/Controllers/Api/Traveler/WalletController.php`
**Depends on**: T1

```php
public function show(Request $request) {
    $wallet = $request->user()->wallet()->with(['transactions' => function($q) {
        $q->orderBy('created_at', 'desc')->limit(20);
    }])->firstOrFail();

    $transactions = $wallet->transactions->map(function($t) {
        return [
            'id' => $t->id,
            'type' => $t->type,
            'amount' => $t->amount,
            'description' => $t->description,
            'created_at' => $t->created_at->format('d/m/Y'),
        ];
    });

    return response()->json([
        'wallet' => ['id' => $wallet->id, 'balance' => $wallet->balance],
        'transactions' => $transactions,
    ]);
}
```

**Done when**:
- [ ] Controller criado
- [ ] Retorna balance e transactions (últimas 20)

---

### T4: MockDataGenerator service [P]
**What**: Service que gera resultados mockados por modal e destino com mix acima/abaixo do teto
**Where**: `app/Services/MockDataGenerator.php`
**Depends on**: T1

Gerar dados realistas para: hotel, flight, bus, car
- Garantir: 2-3 opções abaixo do teto, 2 acima (quando teto existe)
- Incluir: rating, amenities, address, horários (para aéreo/ônibus)

**Done when**:
- [ ] Service criado com método `generate(modal, city, policyMax)`
- [ ] Dados para os 4 modais
- [ ] Mix de preços: sempre inclui opções abaixo e acima do teto
- [ ] Nomes de providers reais brasileiros

---

### T5: SearchController [P]
**What**: Controller que busca política, gera mock, calcula onhappy coins e ordena resultados
**Where**: `app/Http/Controllers/Api/Traveler/SearchController.php`
**Depends on**: T4

```php
public function index(Request $request) {
    $data = $request->validate([
        'modal' => 'required|in:hotel,flight,bus,car',
        'destination_city' => 'required|string',
        'destination_state' => 'required|string|max:2',
        'check_in' => 'required|date',
        'check_out' => 'nullable|date',
    ]);

    $user = $request->user()->load('company');
    $policy = TravelPolicy::where('company_id', $user->company_id)
        ->where('destination_city', $data['destination_city'])
        ->where('destination_state', $data['destination_state'])
        ->first();

    $policyMax = match($data['modal']) {
        'hotel' => $policy?->max_daily_hotel,
        'flight' => $policy?->max_flight,
        default => null,
    };

    $results = app(MockDataGenerator::class)->generate($data['modal'], $data['destination_city'], $policyMax);

    // Calculate onhappy coins per result
    $results = collect($results)->map(function($r) use ($policyMax) {
        $savings = $policyMax && $r['price'] < $policyMax ? round($policyMax - $r['price'], 2) : 0;
        $onhappyCoins = round($savings * 0.5, 2);
        return array_merge($r, [
            'has_onhappy_coins' => $onhappyCoins >= 1.0,
            'onhappy_coins_amount' => $onhappyCoins,
            'savings_total' => $savings,
            'company_savings' => round($savings * 0.5, 2),
        ]);
    })->sortByDesc(fn($r) => $r['has_onhappy_coins'] ? $r['onhappy_coins_amount'] : -$r['price'])->values();

    return response()->json([
        'results' => $results,
        'policy' => $policy ? [
            'id' => $policy->id,
            'max_daily_hotel' => $policy->max_daily_hotel,
            'max_flight' => $policy->max_flight,
        ] : null,
        'destination' => $data['destination_city'].' - '.$data['destination_state'],
    ]);
}
```

**Done when**:
- [ ] Controller criado
- [ ] Busca policy correta para a empresa do usuário
- [ ] Onhappy coins calculado por resultado
- [ ] Ordenação: onhappy coins primeiro (maior primeiro), depois sem onhappy coins
- [ ] Retorna `results`, `policy`, `destination`

---

### T6: Registrar controllers nas rotas (update T1)
**What**: Importar os novos controllers no routes/api.php
**Where**: `routes/api.php`
**Depends on**: T2, T3, T4, T5

Adicionar `use` statements para os 3 novos controllers.

**Done when**:
- [ ] `php artisan route:list` mostra as 3 rotas sem erros de classe não encontrada

---

### T7: Criar diretório de controllers Traveler
**What**: Mover/criar controllers na estrutura `Api/Traveler/`
**Where**: `app/Http/Controllers/Api/Traveler/`
**Depends on**: T6

**Done when**:
- [ ] Diretório `Api/Traveler/` existe com SearchController, BookingController, WalletController
- [ ] Namespaces corretos: `App\Http\Controllers\Api\Traveler`

---

### T8: useSearchStore (Pinia) [P]
**What**: Store de busca com estado do form, resultados, loading e booking
**Where**: `resources/js/stores/search.js`
**Depends on**: None (frontend independente do backend durante dev)

```js
export const useSearchStore = defineStore('search', {
  state: () => ({
    form: { modal: 'hotel', destination_city: '', destination_state: '', check_in: '', check_out: '' },
    results: [],
    policy: null,
    destination: '',
    loading: false,
    error: null,
    selectedResult: null,
    showConfirmModal: false,
    lastBooking: null,
  }),
  actions: {
    async search() { /* GET /api/traveler/search */ },
    selectResult(result) { this.selectedResult = result; this.showConfirmModal = true },
    async confirmBooking() { /* POST /api/traveler/bookings */ },
    reset() { /* limpa estado */ },
  }
})
```

**Done when**:
- [ ] Store criada com todos os campos de state
- [ ] `search()` chama API e popula `results` + `policy`
- [ ] `confirmBooking()` chama API e retorna `onhappy_coins_earned` + `new_wallet_balance`
- [ ] Loading states corretos

---

### T9: SearchForm.vue [P]
**What**: Formulário de busca com campos dinâmicos por modal
**Where**: `resources/js/components/search/SearchForm.vue`
**Depends on**: T8

- Select de modal (hotel/aéreo/ônibus/automóvel) com ícones Lucide
- Select de estado (UF) + autocomplete de cidade por estado (select estado → autocomplete cidade)
- Datas dinâmicas por modal (check-in/check-out para hotel; ida/volta para aéreo)
- Botão Buscar com loading state
- Design system: BaseCard, BaseButton, BaseInput

**Done when**:
- [ ] Campos mudam conforme modal selecionado
- [ ] Submit chama `store.search()`
- [ ] Loading state no botão durante busca
- [ ] Validação: campos obrigatórios não vazios

---

### T10: OnhappyCoinsBadge.vue [P]
**What**: O selo signature do produto — verde pulsante com valor de onhappy coins
**Where**: `resources/js/components/search/OnhappyCoinsBadge.vue`
**Depends on**: None

```vue
<!-- Exemplo de output visual -->
<div class="inline-flex items-center gap-2 px-3 py-2 bg-green-50 border border-green-300 rounded-xl animate-pulse">
  <span class="text-green-600">✦</span>
  <div>
    <p class="text-xs text-green-600 font-medium">Escolha esta opção e ganhe</p>
    <p class="text-lg font-bold text-green-700 font-mono">R$ {{ coinsAmount }}</p>
  </div>
</div>
<!-- Tooltip no hover: breakdown savings/onhappy coins/empresa -->
```

Props: `coinsAmount`, `savingsTotal`, `companyAmount`

**Done when**:
- [ ] Badge verde com pulse animation
- [ ] Valor em JetBrains Mono bold
- [ ] Tooltip/breakdown no hover (v-tooltip ou CSS)
- [ ] Contraste ≥ 4.5:1

---

### T11: ResultCard.vue [P]
**What**: Card de resultado com info do provider + OnhappyCoinsBadge condicional + botão Reservar
**Where**: `resources/js/components/search/ResultCard.vue`
**Depends on**: T10

- Nome do provider, endereço, rating (estrelas)
- Preço em destaque (JetBrains Mono)
- OnhappyCoinsBadge se `result.has_onhappy_coins`
- Borda verde intensa se onhappy coins > R$ 100
- Botão "Reservar" que emite `select`

**Done when**:
- [ ] Card exibe todas as infos do resultado
- [ ] OnhappyCoinsBadge aparece apenas quando has_onhappy_coins=true
- [ ] Borda enfatizada para onhappy coins grande
- [ ] Emite evento `select` com o result

---

### T12: BookingModal.vue [P]
**What**: Modal de confirmação com split financeiro detalhado
**Where**: `resources/js/components/search/BookingModal.vue`
**Depends on**: T10

- Overlay escuro com modal centralizado
- Header: nome do provider + modal
- Preço pago, teto da política
- Split visual: "Você ganha R$X" (verde) | "Empresa economiza R$Y" (azul)
- Botão "Confirmar Reserva" com loading + botão "Cancelar"
- Fecha com Escape ou clique no overlay

**Done when**:
- [ ] Modal abre/fecha corretamente
- [ ] Split financeiro exibido visualmente
- [ ] Botão confirmar emite `confirm` e mostra loading
- [ ] Fecha com Escape e click overlay

---

### T13: SearchPage.vue — integração completa
**What**: Página de busca que orquestra form + resultados + modal + toast de sucesso
**Where**: `resources/js/pages/traveler/SearchPage.vue`
**Depends on**: T8, T9, T11, T12

- Layout: AppShell wrapper
- Estado inicial: SearchForm centralizado
- Após busca: SearchForm no topo + ResultsList abaixo
- BookingModal quando `store.showConfirmModal`
- Toast de sucesso após reserva: "🎉 Você ganha onhappy coins na sua carteira!"
- Após confirmar: redirect para WalletPage

**Done when**:
- [ ] SearchForm visível sempre
- [ ] ResultsList aparece após busca com resultados
- [ ] Modal abre ao clicar Reservar
- [ ] Toast aparece após booking confirmado
- [ ] Redirect para /traveler/wallet após 2s do toast

---

### T14: WalletBalance.vue + TransactionList.vue [P]
**What**: Componentes da página de carteira
**Where**: `resources/js/components/wallet/`
**Depends on**: None

**WalletBalance**: Card hero com saldo em destaque
- Saldo em JetBrains Mono 36px, cor reward (verde)
- Subtítulo "Saldo disponível para viagens de lazer"
- Ícone Wallet da Lucide

**TransactionList**: Lista de transações
- Cada item: ícone de crédito (verde +), data, descrição, valor
- Empty state: ilustração + "Faça sua primeira reserva econômica"

**Done when**:
- [ ] WalletBalance.vue com saldo formatado como R$ X.XXX,XX
- [ ] TransactionList.vue com estados: loading, empty, populated

---

### T15: WalletPage.vue — integração completa [P]
**What**: Página da carteira que busca dados e exibe saldo + histórico
**Where**: `resources/js/pages/traveler/WalletPage.vue`
**Depends on**: T3, T14

- AppShell wrapper
- Chama `GET /api/traveler/wallet` ao montar
- Exibe WalletBalance + TransactionList
- Loading skeleton enquanto carrega

**Done when**:
- [ ] Página carrega dados reais da API
- [ ] Saldo e transações exibidos
- [ ] Loading state durante fetch
- [ ] Após booking: saldo atualizado reflete imediatamente (store.auth.wallet atualizado)

---

## Parallel Execution Map

```
T1 (routes)
  ├── T2 (BookingController) ──┐
  ├── T3 (WalletController)  ──┤
  ├── T4 (MockDataGenerator) ──┤→ T6 (imports) → T7 (verify)
  └── T5 (SearchController)  ──┘

T8 (search store) ──┐
T9 (SearchForm)  ──┼→ T11 (ResultCard) ──┐
T10 (OnhappyCoinsBadge)─┘                    ├→ T13 (SearchPage)
                     T12 (BookingModal) ──┘

T14 (wallet components) ──┐
T3  (WalletController)   ──┤→ T15 (WalletPage)
```

**Total: 15 tarefas**
