<template>
  <!-- Overlay -->
  <Teleport to="body">
    <div
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      @keydown.escape="$emit('cancel')"
      tabindex="-1"
    >
      <!-- Backdrop -->
      <div
        class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
        @click="$emit('cancel')"
      />

      <!-- Modal -->
      <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="p-6 border-b border-slate-100">
          <div class="flex items-start justify-between">
            <div>
              <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Confirmação de reserva</p>
              <h2 class="text-lg font-bold text-slate-900 leading-tight">{{ result.name }}</h2>
              <p class="text-sm text-slate-500 mt-0.5">{{ result.address }}</p>
            </div>
            <button
              @click="$emit('cancel')"
              class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-all shrink-0"
            >
              <X class="w-4 h-4" />
            </button>
          </div>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4">
          <!-- Price row -->
          <div class="flex items-center justify-between py-3 border-b border-slate-100">
            <span class="text-sm text-slate-600">Valor da reserva</span>
            <span class="text-xl font-bold text-slate-900 font-mono">{{ formatCurrency(result.price) }}</span>
          </div>

          <!-- Onhappy coins card -->
          <div v-if="result.has_onhappy_coins" class="rounded-xl bg-green-50 border border-green-200 p-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-green-100 flex items-center justify-center">
                  <span class="text-green-600 text-sm">✦</span>
                </div>
                <div>
                  <p class="text-sm font-semibold text-green-700">Você ganha onhappy coins na sua carteira!</p>
                </div>
              </div>
              <span class="text-2xl font-bold text-green-700 font-mono">{{ result.onhappy_coins_amount }}</span>
            </div>
          </div>

          <!-- Dates -->
          <div class="flex gap-3 text-sm text-slate-600">
            <div v-if="checkin">
              <p class="text-xs text-slate-400 font-medium">Check-in / Ida</p>
              <p class="font-medium text-slate-700">{{ formatDate(checkin) }}</p>
            </div>
            <div v-if="checkout">
              <p class="text-xs text-slate-400 font-medium">Check-out / Volta</p>
              <p class="font-medium text-slate-700">{{ formatDate(checkout) }}</p>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 pb-6 flex gap-3">
          <button
            @click="$emit('cancel')"
            class="flex-1 h-11 border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 transition-all"
          >
            Cancelar
          </button>
          <button
            @click="$emit('confirm')"
            :disabled="loading"
            :class="[
              'flex-1 h-11 flex items-center justify-center gap-2 text-white text-sm font-semibold rounded-xl transition-all disabled:opacity-60',
              result.has_onhappy_coins ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700'
            ]"
          >
            <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
            </svg>
            {{ loading ? 'Confirmando...' : result.has_onhappy_coins ? '✦ Confirmar e ganhar onhappy coins' : 'Confirmar reserva' }}
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { X } from 'lucide-vue-next'

const props = defineProps({
  result:   { type: Object, required: true },
  checkin:  { type: String, default: '' },
  checkout: { type: String, default: '' },
  loading:  { type: Boolean, default: false },
})

defineEmits(['confirm', 'cancel'])

function formatCurrency(value) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value ?? 0)
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  const [y, m, d] = dateStr.split('-')
  return `${d}/${m}/${y}`
}
</script>
