<template>
  <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100">
      <h3 class="text-base font-semibold text-slate-900">Histórico de créditos</h3>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="p-5 space-y-3">
      <div v-for="i in 3" :key="i" class="flex items-center gap-3 animate-pulse">
        <div class="w-10 h-10 rounded-full bg-slate-200 shrink-0"></div>
        <div class="flex-1 space-y-1.5">
          <div class="h-4 bg-slate-200 rounded w-2/3"></div>
          <div class="h-3 bg-slate-100 rounded w-1/3"></div>
        </div>
        <div class="h-5 bg-slate-200 rounded w-20"></div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else-if="!transactions.length" class="flex flex-col items-center justify-center py-16 px-6 text-center">
      <div class="text-5xl mb-4">💳</div>
      <h4 class="text-base font-semibold text-slate-700 mb-2">Nenhum crédito ainda</h4>
      <p class="text-sm text-slate-500">Faça sua primeira reserva econômica para ganhar onhappy coins</p>
    </div>

    <!-- List -->
    <ul v-else class="divide-y divide-slate-100">
      <li v-for="tx in transactions" :key="tx.id" class="flex items-center gap-4 px-5 py-4">
        <!-- Icon -->
        <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center shrink-0">
          <span class="text-green-600 font-bold text-sm">+</span>
        </div>

        <!-- Info -->
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-slate-800 truncate">{{ tx.description }}</p>
          <p class="text-xs text-slate-400 mt-0.5">{{ tx.created_at }}</p>
        </div>

        <!-- Right: amount -->
        <div class="text-right shrink-0">
          <p class="text-sm font-bold text-green-700 font-mono">+{{ formatCurrency(tx.amount) }}</p>
          <p class="text-xs text-slate-400 mt-0.5">Você ganha OnhappyCoins na sua carteira</p>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup>
defineProps({
  transactions: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
})

function formatCurrency(v) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v)
}
</script>
