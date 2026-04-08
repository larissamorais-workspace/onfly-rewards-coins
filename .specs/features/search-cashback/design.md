# Busca com Selos de Onhappy Coins — Design

**Spec**: `.specs/features/search-cashback/spec.md`
**Status**: Draft

---

## Architecture Overview

```mermaid
graph TD
    subgraph Frontend
        SP[SearchPage.vue] --> SF[SearchForm.vue]
        SP --> RL[ResultsList.vue]
        RL --> RC[ResultCard.vue]
        RC --> CB[OnhappyCoinsBadge.vue]
        RC --> BM[BookingModal.vue]
        WP[WalletPage.vue] --> WB[WalletBalance.vue]
        WP --> TL[TransactionList.vue]
    end

    subgraph Backend
        SR[SearchController@index] --> MG[MockDataGenerator]
        MG --> PC[PolicyChecker]
        BC[BookingController@store] --> SP2[SplitProcessor]
        SP2 --> WT[WalletTransaction]
    end

    SF -->|GET /api/traveler/search| SR
    BM -->|POST /api/traveler/bookings| BC
    WP -->|GET /api/traveler/wallet| WP2[WalletController@show]
```

---

## Tech Decisions

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Dados de busca | Mock gerado no backend por destino+modal | Sem API real; dados convincentes para hackathon |
| Cálculo de onhappy coins | Backend (authoritative) | Frontend mostra preview; backend valida e processa |
| Ordenação resultados | Backend: onhappy coins primeiro, maior primeiro | Lógica de negócio fica no servidor |
| Estado de busca | Pinia store separada (`useSearchStore`) | Isola estado da busca do estado de auth |
| Modal de confirmação | Componente Vue inline na SearchPage | Sem library externa; mais rápido para hackathon |

---

## Components

### Backend

#### `SearchController` (`app/Http/Controllers/Api/Traveler/SearchController.php`)
- **Purpose**: Retorna resultados mockados com onhappy coins calculados
- **Endpoint**: `GET /api/traveler/search`
- **Query params**: `modal`, `destination_city`, `destination_state`, `check_in`, `check_out`
- **Logic**:
  1. Busca `TravelPolicy` para o destino da empresa do usuário
  2. Gera mock results via `MockDataGenerator`
  3. Para cada resultado: calcula `has_onhappy_coins`, `onhappy_coins_amount`, `savings_total`
  4. Ordena: com onhappy coins primeiro (maior valor no topo), depois sem onhappy coins (menor preço)
  5. Retorna array de resultados + policy info

#### `MockDataGenerator` (`app/Services/MockDataGenerator.php`)
- **Purpose**: Gera resultados simulados realistas por modal e destino
- **Interface**: `generate(string $modal, string $city, float $policyMax): array`
- **Datasets**:
  - hotel: Ibis, Mercure, Bristol, Slaviero, Novotel, Golden Tulip, Comfort — com preços variados
  - flight: LATAM, Gol, Azul — com horários e escalas simuladas
  - bus: Cometa, Itapemirim, Catarinense, Eucatur — preços baixos
  - car: Localiza, Movida, Unidas, Hertz — por categoria (econômico, intermediário, SUV)
- **Price strategy**: Mix garantido — 2-3 opções abaixo do teto, 2 acima

#### `BookingController` (`app/Http/Controllers/Api/Traveler/BookingController.php`)
- **Purpose**: Cria reserva e processa split financeiro
- **Endpoint**: `POST /api/traveler/bookings`
- **Body**: `{ modal, provider_name, destination_city, destination_state, paid_price, original_price, check_in, check_out, travel_policy_id }`
- **Logic**:
  1. Valida dados
  2. Busca TravelPolicy para calcular teto
  3. Calcula split: `savings = teto - paid_price`, `onhappy_coins = savings * 0.5`, `company_savings = savings * 0.5`
  4. Cria `Booking`
  5. Se onhappy_coins > 0: cria `WalletTransaction` (credit, sem expires_at — créditos permanentes), incrementa `Wallet.balance`
  6. Retorna `{ booking, onhappy_coins_earned, new_wallet_balance }`

#### `WalletController` (`app/Http/Controllers/Api/Traveler/WalletController.php`)
- **Purpose**: Retorna saldo e histórico de transações
- **Endpoint**: `GET /api/traveler/wallet`
- **Returns**: `{ wallet: { balance }, transactions: [...] }`

### Frontend

#### `useSearchStore` (`resources/js/stores/search.js`)
- **State**: `form`, `results`, `policy`, `loading`, `error`, `selectedResult`, `showConfirmModal`
- **Actions**: `search()`, `confirmBooking()`, `resetSearch()`

#### `SearchForm.vue` (`resources/js/components/search/SearchForm.vue`)
- **Purpose**: Formulário de busca com campos dinâmicos por modal e autocomplete de cidade por estado
- **Props**: none (usa store)
- **Behavior**: Select de estado → autocomplete de cidade filtrado pelo estado; campos de data mudam por modal selecionado; submete via `store.search()`

#### `ResultCard.vue` (`resources/js/components/search/ResultCard.vue`)
- **Purpose**: Card de resultado com info do provider + selo de onhappy coins
- **Props**: `result` (object), `policy` (object)
- **Emits**: `select` → abre modal de confirmação

#### `OnhappyCoinsBadge.vue` (`resources/js/components/search/OnhappyCoinsBadge.vue`)
- **Purpose**: O selo verde pulsante — elemento signature do produto
- **Props**: `coinsAmount` (number), `savingsTotal` (number)
- **Behavior**: Pulse animation, breakdown no hover (tooltip), contraste 4.8:1

#### `BookingModal.vue` (`resources/js/components/search/BookingModal.vue`)
- **Purpose**: Modal de confirmação com resumo e split financeiro
- **Props**: `result`, `policy`
- **Emits**: `confirm`, `cancel`
- **Shows**: Provider, preço, onhappy coins a receber, economia da empresa, data

#### `WalletBalance.vue` (`resources/js/components/wallet/WalletBalance.vue`)
- **Purpose**: Card hero com saldo em destaque
- **Props**: `balance` (number)

#### `TransactionList.vue` (`resources/js/components/wallet/TransactionList.vue`)
- **Purpose**: Lista de transações com ícone e valor em onhappy coins
- **Props**: `transactions` (array)

---

## Data Models (API Response)

### Search Result
```js
{
  id: string,           // uuid gerado
  provider_name: string,
  modal: 'hotel'|'flight'|'bus'|'car',
  price: number,        // preço cobrado
  original_price: number,
  rating: number,       // 1-5
  address: string,      // bairro/aeroporto
  amenities: string[],  // ['Wi-Fi', 'Café da manhã', ...]
  has_onhappy_coins: boolean,
  onhappy_coins_amount: number,   // (teto - price) * 0.5
  savings_total: number,          // teto - price
  company_savings: number,        // (teto - price) * 0.5
}
```

### Booking Response
```js
{
  booking: { id, status, provider_name, paid_price, ... },
  onhappy_coins_earned: number,
  new_wallet_balance: number,
}
```

### Wallet Response
```js
{
  wallet: { id, balance: number },
  transactions: [{
    id, type, amount, description,
    created_at,
  }]
}
```

---

## UI Flow

```
SearchPage
  ↓ [preenche form]
SearchForm → store.search() → GET /api/traveler/search
  ↓ [resultados carregados]
ResultsList → exibe 4-6 ResultCards
  ↓ [cards com onhappy coins têm OnhappyCoinsBadge pulsante]
  ↓ [clica "Reservar"]
BookingModal → exibe split: onhappy coins X | empresa economiza R$Y
  ↓ [confirma]
store.confirmBooking() → POST /api/traveler/bookings
  ↓ [sucesso]
Toast "🎉 Você ganha onhappy coins na sua carteira!"
  ↓ [redirect]
WalletPage → WalletBalance (saldo atualizado) + TransactionList
```

---

## Mock Data Strategy por Modal

| Modal | Providers | Price Range vs Teto |
|-------|-----------|---------------------|
| Hotel (SP, teto R$750) | Ibis R$280, Mercure R$420, Bristol R$390, Slaviero R$510, Novotel R$680, Golden Tulip R$790, Comfort R$320 | Mix garantido |
| Aéreo (SP, teto R$1200) | LATAM R$890 6h, Gol R$760 direto, Azul R$1050 1 escala, LATAM R$1380 direto | Mix |
| Ônibus (SP, teto —) | Cometa R$89, Itapemirim R$105, Catarinense R$79 | Sempre abaixo |
| Carro (SP, teto —) | Localiza Econômico R$180/dia, Movida Intermediário R$220/dia, Unidas SUV R$380/dia | Variado |

---

## Error Handling

| Scenario | Handling |
|----------|----------|
| Destino sem política | Retorna results sem onhappy coins + `policy: null` no response |
| Booking falha | Toast de erro, modal permanece aberto |
| Wallet sem transações | Empty state ilustrado |
| Search sem resultados | Empty state com sugestão de outro destino |

---

## Requirement Mapping

| ID | Requirement | Component |
|----|-------------|-----------|
| SRCH-01 | Formulário de Busca | SearchForm.vue |
| SRCH-02 | Lista com Selos | ResultCard + OnhappyCoinsBadge |
| SRCH-03 | Confirmação de Reserva | BookingModal + BookingController |
| SRCH-04 | Backend Mock Data | SearchController + MockDataGenerator |
| SRCH-05 | Backend Split | BookingController + SplitProcessor |
| SRCH-06 | Ordenação | SearchController@index (sorting logic) |
| SRCH-07 | WalletPage | WalletController + WalletBalance + TransactionList |
