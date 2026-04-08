<template>
  <div :class="[
    'flex items-center gap-4 px-5 py-4 rounded-2xl border transition-all',
    position === 1 ? 'bg-amber-50 border-amber-200' :
    position === 2 ? 'bg-slate-50 border-slate-200' :
    position === 3 ? 'bg-orange-50 border-orange-200' :
    'bg-white border-slate-100'
  ]">
    <!-- Position medal -->
    <div class="w-10 text-center shrink-0">
      <span v-if="position === 1" class="text-2xl">🥇</span>
      <span v-else-if="position === 2" class="text-2xl">🥈</span>
      <span v-else-if="position === 3" class="text-2xl">🥉</span>
      <span v-else class="text-lg font-bold text-slate-400">{{ position }}</span>
    </div>

    <!-- Avatar + name -->
    <div :class="[
      'w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shrink-0',
      position === 1 ? 'bg-amber-200 text-amber-800' :
      position === 2 ? 'bg-slate-300 text-slate-700' :
      position === 3 ? 'bg-orange-200 text-orange-800' :
      'bg-blue-100 text-blue-700'
    ]">
      {{ initials }}
    </div>

    <div class="flex-1 min-w-0">
      <p class="text-sm font-semibold text-slate-900 truncate">{{ name }}</p>
      <p class="text-xs text-slate-500 truncate">{{ positionTitle }} · {{ department }}</p>
    </div>

    <!-- Stats -->
    <div class="text-right shrink-0 space-y-0.5">
      <p class="text-sm font-bold text-blue-700 font-mono">{{ formatCurrency(totalCompanySavings) }}</p>
      <p class="text-xs text-slate-400">economia p/ empresa</p>
    </div>
    <div class="text-right shrink-0 space-y-0.5 hidden sm:block">
      <p class="text-sm font-bold text-green-700 font-mono">{{ totalOnhappyCoinsEarned }}</p>
      <p class="text-xs text-slate-400">onhappy coins</p>
    </div>
    <div class="text-right shrink-0 space-y-0.5 hidden md:block">
      <p class="text-sm font-bold text-slate-700">{{ totalBookings }}</p>
      <p class="text-xs text-slate-400">reservas</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  position:             { type: Number, required: true },
  name:                 { type: String, required: true },
  positionTitle:        { type: String, default: '' },
  department:           { type: String, default: '' },
  totalCompanySavings:  { type: Number, default: 0 },
  totalOnhappyCoinsEarned: { type: Number, default: 0 },
  totalBookings:        { type: Number, default: 0 },
})

const initials = computed(() =>
  props.name.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase()
)

function formatCurrency(v) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v)
}
</script>
