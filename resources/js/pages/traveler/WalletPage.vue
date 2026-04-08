<template>
  <AppShell>
    <div class="w-full space-y-6">
      <!-- Balance -->
      <WalletBalance :balance="wallet?.balance ?? 0" />

      <!-- Summary cards -->
      <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-slate-100 p-4 text-center">
          <p class="text-xs text-slate-500 font-medium mb-1">Reservas</p>
          <p class="text-2xl font-bold text-slate-900 font-mono">{{ bookings.length }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 p-4 text-center">
          <p class="text-xs text-slate-500 font-medium mb-1">Total ganho</p>
          <p class="text-xl font-bold text-green-700 font-mono">{{ formatCurrency(totalCoins) }}</p>
        </div>
      </div>

      <!-- Booking history -->
      <div>
        <h2 class="text-base font-semibold text-slate-900 mb-3" style="font-family: var(--font-display);">Histórico de reservas</h2>

        <!-- Loading -->
        <div v-if="loading" class="space-y-3">
          <div v-for="i in 3" :key="i" class="bg-white rounded-xl border border-slate-100 p-5 animate-pulse h-24"></div>
        </div>

        <!-- Empty state -->
        <div v-else-if="!bookings.length" class="flex flex-col items-center justify-center py-12 text-center">
          <div class="text-4xl mb-3">🏨</div>
          <h3 class="text-base font-semibold text-slate-700 mb-1">Nenhuma reserva ainda</h3>
          <p class="text-sm text-slate-500 mb-4">Faça sua primeira reserva econômica para ganhar onhappy coins</p>
          <RouterLink
            to="/traveler/search"
            class="px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all"
          >
            Buscar hospedagem
          </RouterLink>
        </div>

        <!-- Booking list -->
        <div v-else class="space-y-3">
          <div
            v-for="booking in bookings"
            :key="booking.id"
            :class="[
              'bg-white rounded-xl border-2 p-5 space-y-3',
              booking.has_onhappy_coins ? 'border-green-200' : 'border-slate-100'
            ]"
          >
            <!-- Top row -->
            <div class="flex items-start justify-between gap-3">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                  <Hotel class="w-5 h-5 text-blue-600" />
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

            <!-- Bottom row -->
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
    </div>
  </AppShell>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import axios from 'axios'
import { Hotel } from 'lucide-vue-next'
import AppShell from '../../layouts/AppShell.vue'
import WalletBalance from '../../components/wallet/WalletBalance.vue'

const loading = ref(true)
const wallet = ref(null)
const bookings = ref([])

const totalCoins = computed(() =>
  bookings.value.reduce((s, b) => s + parseFloat(b.onhappy_coins_amount ?? 0), 0)
)

function formatCurrency(v) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v)
}

onMounted(async () => {
  try {
    const res = await axios.get('/traveler/wallet')
    wallet.value = res.data.wallet
    bookings.value = res.data.bookings ?? []
  } catch (e) {
    console.error('Erro ao carregar carteira', e)
  } finally {
    loading.value = false
  }
})
</script>
