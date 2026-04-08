<template>
  <AppShell>
    <div class="max-w-3xl mx-auto space-y-6">
      <!-- Header -->
      <div>
        <h1 class="text-2xl md:text-3xl font-bold text-slate-900" style="font-family: var(--font-display); letter-spacing: -0.025em;">Meu Histórico</h1>
        <p class="text-sm text-slate-500 mt-1">Todas as suas reservas corporativas</p>
      </div>

      <!-- Summary bar -->
      <div v-if="!loading && bookings.length" class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-100 p-4 text-center">
          <p class="text-xs text-slate-500 font-medium mb-1">Reservas</p>
          <p class="text-2xl font-bold text-slate-900 font-mono">{{ bookings.length }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 p-4 text-center">
          <p class="text-xs text-slate-500 font-medium mb-1">Total onhappy coins</p>
          <p class="text-lg font-bold text-green-700 font-mono">{{ formatCurrency(totalOnhappyCoins) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 p-4 text-center">
          <p class="text-xs text-slate-500 font-medium mb-1">Economia p/ empresa</p>
          <p class="text-lg font-bold text-blue-700 font-mono">{{ formatCurrency(totalCompany) }}</p>
        </div>
      </div>

      <!-- Loading skeleton -->
      <div v-if="loading" class="space-y-3">
        <div v-for="i in 4" :key="i" class="bg-white rounded-2xl border border-slate-100 p-5 animate-pulse h-24"></div>
      </div>

      <!-- Empty state -->
      <div v-else-if="!bookings.length" class="flex flex-col items-center justify-center py-20 text-center">
        <div class="text-5xl mb-4">🗂️</div>
        <h3 class="text-lg font-semibold text-slate-700 mb-2">Nenhuma reserva ainda</h3>
        <p class="text-sm text-slate-500 mb-5">Faça sua primeira reserva na tela de busca.</p>
        <RouterLink
          to="/traveler/search"
          class="px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all"
        >
          Buscar viagem
        </RouterLink>
      </div>

      <!-- Booking list -->
      <div v-else class="space-y-3">
        <div
          v-for="booking in bookings"
          :key="booking.id"
          :class="[
            'bg-white rounded-2xl border-2 p-5 space-y-3',
            booking.has_onhappy_coins ? 'border-green-200' : 'border-slate-100'
          ]"
        >
          <!-- Top row: icon + provider + status -->
          <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
              <div :class="['w-10 h-10 rounded-xl flex items-center justify-center shrink-0', modalBg(booking.modal)]">
                <component :is="modalIcon(booking.modal)" class="w-5 h-5" :class="modalColor(booking.modal)" />
              </div>
              <div class="min-w-0">
                <p class="text-sm font-semibold text-slate-900 truncate">{{ booking.provider_name }}</p>
                <p class="text-xs text-slate-500">{{ booking.destination }}</p>
              </div>
            </div>
            <span :class="[
              'shrink-0 text-xs font-medium px-2.5 py-1 rounded-full',
              booking.status === 'confirmed' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600'
            ]">
              {{ booking.status === 'confirmed' ? 'Confirmada' : 'Cancelada' }}
            </span>
          </div>

          <!-- Bottom row: dates + prices -->
          <div class="flex items-end justify-between gap-3 pt-2 border-t border-slate-100">
            <div class="space-y-0.5">
              <p class="text-xs text-slate-400">
                {{ booking.check_in }}{{ booking.check_out ? ' → ' + booking.check_out : '' }}
              </p>
              <p class="text-sm font-semibold text-slate-700 font-mono">{{ formatCurrency(booking.paid_price) }}</p>
            </div>
            <div v-if="booking.has_onhappy_coins" class="flex items-center gap-1.5 bg-green-50 border border-green-200 rounded-xl px-3 py-1.5">
              <span class="text-green-600 text-sm">✦</span>
              <span class="text-sm font-bold text-green-700 font-mono">+{{ booking.onhappy_coins_amount }}</span>
              <span class="text-xs text-green-600">onhappy coins</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppShell>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import axios from 'axios'
import { Hotel, Plane, Bus, Car } from 'lucide-vue-next'
import AppShell from '../../layouts/AppShell.vue'

const loading = ref(true)
const bookings = ref([])

const totalOnhappyCoins = computed(() => bookings.value.reduce((s, b) => s + (b.onhappy_coins_amount ?? 0), 0))
const totalCompany  = computed(() => bookings.value.reduce((s, b) => s + (b.company_savings ?? 0), 0))

function formatCurrency(v) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v ?? 0)
}

function modalIcon(m)  { return { hotel: Hotel, flight: Plane, bus: Bus, car: Car }[m] ?? Plane }
function modalBg(m)    { return { hotel: 'bg-blue-50', flight: 'bg-purple-50', bus: 'bg-amber-50', car: 'bg-slate-100' }[m] ?? 'bg-slate-100' }
function modalColor(m) { return { hotel: 'text-blue-600', flight: 'text-purple-600', bus: 'text-amber-600', car: 'text-slate-600' }[m] ?? 'text-slate-600' }

onMounted(async () => {
  try {
    const res = await axios.get('/traveler/history')
    bookings.value = res.data.bookings
  } catch (e) {
    console.error('Erro ao carregar histórico', e)
  } finally {
    loading.value = false
  }
})
</script>
