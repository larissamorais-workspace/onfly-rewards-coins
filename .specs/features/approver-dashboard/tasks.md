# Approver Dashboard Tasks

## Execution Plan

```
T1 (BookingSeeder) ──────────────────────────────────────────┐
T2 (rotas approver) → T3 (DashboardController) ─────────────┤→ T6 (DashboardPage)
                    → T4 (RankingController)   ─────────────┤→ T7 (RankingPage)
                    → T5 (Componentes UI)      ─────────────┘
```

Todos paralelos após T2.

---

### T1: BookingSeeder — histórico simulado
**What**: Criar BookingSeeder com 15-20 bookings distribuídos entre 5 viajantes, com onhappy coins processado
**Where**: `database/seeders/BookingSeeder.php` + atualizar `DatabaseSeeder.php`

Bookings por viajante (user index 0-4 = isabela, rafael, priscila, lucas, mariana):
- isabela: 4 bookings (3 hotel SP/RJ com onhappy coins, 1 aéreo sem onhappy coins)
- rafael: 4 bookings (2 hotel Curitiba com onhappy coins, 1 ônibus, 1 aéreo com onhappy coins)
- priscila: 3 bookings (2 hotel BH com onhappy coins, 1 carro)
- lucas: 3 bookings (1 hotel SP com onhappy coins, 1 aéreo, 1 hotel sem onhappy coins)
- mariana: 2 bookings (1 hotel Curitiba com onhappy coins, 1 aéreo sem onhappy coins)

Para cada booking com onhappy coins > 0: criar WalletTransaction + incrementar Wallet.balance.
Truncate: wallet_transactions, wallets (reset balance), bookings — antes de recriar.

**Done when**:
- [ ] 15+ bookings criados com dados realistas
- [ ] Wallet balances atualizados para viajantes com onhappy coins
- [ ] DatabaseSeeder inclui BookingSeeder na ordem correta
- [ ] `php artisan db:seed` roda sem erros

---

### T2: Rotas e stubs — approver controllers
**What**: Criar rotas e stubs para DashboardController e RankingController
**Where**: `routes/api.php`, `app/Http/Controllers/Api/Approver/`

```php
// routes/api.php — approver group
use App\Http\Controllers\Api\Approver\DashboardController;
use App\Http\Controllers\Api\Approver\RankingController;

Route::middleware(['auth:sanctum', 'role:approver'])->prefix('approver')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/ranking', [RankingController::class, 'index']);
});
```

**Done when**:
- [ ] Diretório `Api/Approver/` com stubs
- [ ] `php artisan route:list` mostra approver/dashboard e approver/ranking

---

### T3: DashboardController [P]
**What**: Agrega métricas de economia da empresa do aprovador logado
**Where**: `app/Http/Controllers/Api/Approver/DashboardController.php`

```php
public function index(Request $request) {
    $companyId = $request->user()->company_id;
    $userIds = User::where('company_id', $companyId)->pluck('id');

    $bookings = Booking::whereIn('user_id', $userIds)->get();

    $byModal = $bookings->groupBy('modal')->map(fn($group) => [
        'count'   => $group->count(),
        'savings' => round($group->sum('company_savings'), 2),
    ]);

    return response()->json([
        'total_company_savings'       => round($bookings->sum('company_savings'), 2),
        'total_onhappy_coins_distributed'  => round($bookings->sum('onhappy_coins_amount'), 2),
        'total_bookings_with_savings' => $bookings->where('savings_total', '>', 0)->count(),
        'active_travelers'            => $bookings->pluck('user_id')->unique()->count(),
        'by_modal'                    => $byModal,
    ]);
}
```

**Done when**:
- [ ] Retorna 5 métricas + by_modal breakdown
- [ ] Filtra apenas usuários da empresa do aprovador

---

### T4: RankingController [P]
**What**: Ranking de viajantes ordenado por economia gerada para a empresa
**Where**: `app/Http/Controllers/Api/Approver/RankingController.php`

```php
public function index(Request $request) {
    $companyId = $request->user()->company_id;

    $ranking = User::where('company_id', $companyId)
        ->where('role', 'traveler')
        ->withSum('bookings as total_company_savings', 'company_savings')
        ->withSum('bookings as total_onhappy_coins_earned', 'onhappy_coins_amount')
        ->withCount('bookings as total_bookings')
        ->orderByDesc('total_company_savings')
        ->get()
        ->map(fn($u, $i) => [
            'position'             => $i + 1,
            'user_id'              => $u->id,
            'name'                 => $u->name,
            'position_title'       => $u->position,
            'department'           => $u->department,
            'total_company_savings'=> round($u->total_company_savings ?? 0, 2),
            'total_onhappy_coins_earned'=> round($u->total_onhappy_coins_earned ?? 0, 2),
            'total_bookings'       => $u->total_bookings ?? 0,
        ]);

    return response()->json(['ranking' => $ranking]);
}
```

**Done when**:
- [ ] Retorna ranking com position 1-N
- [ ] Ordenado por total_company_savings DESC
- [ ] Inclui viajantes sem bookings (com valores zerados)

---

### T5: Componentes UI approver [P]
**What**: Criar componentes reutilizáveis para dashboard e ranking
**Where**: `resources/js/components/approver/`

**MetricCard.vue** — card de métrica com ícone, label e valor:
```vue
<template>
  <div class="bg-white rounded-2xl border border-slate-100 p-5">
    <div class="flex items-center gap-3 mb-3">
      <div :class="['w-10 h-10 rounded-xl flex items-center justify-center', iconBg]">
        <component :is="icon" class="w-5 h-5" :class="iconColor" />
      </div>
      <p class="text-sm text-slate-500 font-medium">{{ label }}</p>
    </div>
    <p :class="['text-2xl font-bold font-mono', valueColor]">{{ formattedValue }}</p>
  </div>
</template>
```
Props: `label`, `value`, `format`(currency|number), `icon`, `iconBg`, `iconColor`, `valueColor`

**RankingRow.vue** — linha do leaderboard:
- Posição com medalha (🥇🥈🥉 para top 3, número para o resto)
- Avatar circular com iniciais + nome + cargo
- Valores: total_company_savings (azul), total_onhappy_coins_earned (verde)
- Linha com borda dourada/prateada/bronze para top 3

**Done when**:
- [ ] MetricCard.vue criado com props e slot
- [ ] RankingRow.vue com medalhas para top 3 e highlight visual

---

### T6: DashboardPage.vue [P]
**What**: Página do aprovador com métricas em grid + breakdown por modal
**Where**: `resources/js/pages/approver/DashboardPage.vue`

Layout:
- 4 MetricCards em grid 2x2: Economia Total (azul), Onhappy Coins Distribuído (verde), Reservas com Economia (slate), Viajantes Ativos (slate)
- Seção "Por modal" com 4 mini-cards (hotel, aéreo, ônibus, carro)
- Busca dados de `GET /api/approver/dashboard` no mount

**Done when**:
- [ ] Grid de 4 MetricCards com ícones Lucide
- [ ] Breakdown por modal
- [ ] Loading skeleton enquanto carrega
- [ ] Dados reais da API

---

### T7: RankingPage.vue [P]
**What**: Página de ranking com leaderboard estilizado
**Where**: `resources/js/pages/approver/RankingPage.vue`

Layout:
- Header com título "Ranking de Economia" + subtítulo
- Top 3 em cards de destaque (dourado/prateado/bronze) lado a lado
- Lista completa com RankingRow abaixo
- Busca dados de `GET /api/approver/ranking` no mount

**Done when**:
- [ ] Top 3 com destaque visual diferenciado
- [ ] Lista completa de viajantes
- [ ] Empty state se sem dados
- [ ] Dados reais da API
