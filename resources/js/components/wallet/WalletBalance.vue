<template>
  <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-2xl p-6 text-white">
    <div class="flex items-start justify-between mb-4">
      <div>
        <p class="text-sm text-green-200 font-medium">Saldo disponível</p>
        <p class="text-4xl font-bold font-mono mt-1 tracking-tight" style="font-family: var(--font-display); letter-spacing: -0.04em;">{{ formatCurrency(balance) }}</p>
      </div>
      <div class="w-12 h-12 rounded-xl bg-green-500 flex items-center justify-center">
        <Wallet class="w-6 h-6 text-white" />
      </div>
    </div>
    <p class="text-green-200 text-xs">Seus onhappy coins · Onfly Rewards</p>
  </div>
</template>

<script setup>
import { Wallet } from 'lucide-vue-next'

defineProps({ balance: { type: Number, default: 0 } })

function formatCurrency(v) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v)
}
</script>
