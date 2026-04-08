<template>
  <div :class="[
    'bg-white rounded-2xl border-2 p-5 transition-all duration-200 hover:shadow-md',
    result.has_onhappy_coins && result.onhappy_coins_amount >= 100
      ? 'border-green-300'
      : result.has_onhappy_coins
        ? 'border-green-200'
        : 'border-slate-100',
  ]">
    <div class="flex flex-col gap-4">
      <!-- Header: provider + rating + price -->
      <div class="flex items-start justify-between gap-3">
        <div class="flex-1 min-w-0">
          <h3 class="text-base font-semibold text-slate-900 leading-tight truncate">{{ result.name }}</h3>
          <p class="text-sm text-slate-500 mt-0.5">{{ result.address }}</p>

          <!-- Rating stars -->
          <div class="flex items-center gap-0.5 mt-1.5">
            <span
              v-for="i in 5"
              :key="i"
              :class="i <= result.rating ? 'text-amber-400' : 'text-slate-200'"
              class="text-sm"
            >★</span>
          </div>
        </div>

        <!-- Price -->
        <div class="text-right shrink-0">
          <p class="text-2xl font-bold text-slate-900 font-mono leading-tight">
            {{ formatCurrency(result.price) }}
          </p>
          <p class="text-xs text-slate-400 mt-0.5">{{ priceLabel }}</p>
          <p v-if="result.nights > 1" class="text-sm font-semibold text-slate-600 font-mono mt-1">
            {{ formatCurrency(result.price * result.nights) }}
            <span class="text-xs font-normal text-slate-400">total ({{ result.nights }} diárias)</span>
          </p>
        </div>
      </div>

      <!-- Amenities -->
      <div v-if="result.amenities?.length" class="flex flex-wrap gap-1.5">
        <span
          v-for="amenity in result.amenities.slice(0, 4)"
          :key="amenity"
          class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded-full text-xs font-medium"
        >
          {{ amenity }}
        </span>
      </div>

      <!-- Onhappy Coins badge -->
      <OnhappyCoinsBadge
        v-if="result.has_onhappy_coins"
        :coins-amount="result.onhappy_coins_amount"
        :savings-total="result.savings_total"
      />

      <!-- No onhappy coins note -->
      <div v-else-if="policy" class="text-xs text-slate-400 flex items-center gap-1.5">
        <span class="w-1.5 h-1.5 rounded-full bg-slate-300 shrink-0"></span>
        Preço acima do teto da política
      </div>

      <!-- Reserve button -->
      <button
        @click="$emit('select', result)"
        class="w-full h-10 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 active:bg-blue-800 transition-all"
      >
        {{ result.has_onhappy_coins ? '✦ Reservar e ganhar onhappy coins' : 'Reservar' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import OnhappyCoinsBadge from './OnhappyCoinsBadge.vue'

const props = defineProps({
  result: { type: Object, required: true },
  policy: { type: Object, default: null },
})

defineEmits(['select'])

const policyMax = computed(() => {
  if (!props.policy) return 0
  return props.policy.max_daily_hotel ?? 0
})

const priceLabel = computed(() => 'por noite')

function formatCurrency(value) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value)
}
</script>
