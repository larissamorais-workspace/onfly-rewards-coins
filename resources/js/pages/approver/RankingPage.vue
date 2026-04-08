<template>
  <AppShell>
    <div class="w-full space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl md:text-3xl font-bold text-slate-900" style="font-family: var(--font-display); letter-spacing: -0.025em;">Ranking de Economia</h1>
          <p class="text-sm text-slate-500 mt-1">Viajantes que mais economizaram para a empresa</p>
        </div>
        <div class="text-3xl">🏆</div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="space-y-3">
        <div v-for="i in 5" :key="i" class="bg-white rounded-2xl border border-slate-100 p-5 animate-pulse h-20"></div>
      </div>

      <!-- Empty state -->
      <div v-else-if="!ranking.length" class="flex flex-col items-center justify-center py-20 text-center">
        <div class="text-5xl mb-4">📊</div>
        <h3 class="text-lg font-semibold text-slate-700 mb-2">Nenhuma reserva econômica registrada</h3>
        <p class="text-sm text-slate-500">Quando os viajantes fizerem reservas abaixo do teto, aparecerão aqui.</p>
      </div>

      <!-- Top 3 highlight -->
      <div v-else-if="ranking.length >= 3" class="grid grid-cols-3 gap-4">
        <!-- 2nd place (left) -->
        <div class="bg-slate-50 border-2 border-slate-200 rounded-2xl p-4 text-center flex flex-col items-center gap-2 mt-6">
          <span class="text-3xl">🥈</span>
          <div class="w-12 h-12 rounded-full bg-slate-300 text-slate-700 flex items-center justify-center text-sm font-bold">
            {{ initials(ranking[1]?.name) }}
          </div>
          <p class="text-sm font-semibold text-slate-800 truncate w-full text-center">{{ ranking[1]?.name?.split(' ')[0] }}</p>
          <p class="text-base font-bold text-blue-700 font-mono">{{ formatCurrency(ranking[1]?.total_company_savings) }}</p>
          <p class="text-xs text-slate-400">p/ empresa</p>
        </div>

        <!-- 1st place (center, elevated) -->
        <div class="bg-amber-50 border-2 border-amber-300 rounded-2xl p-4 text-center flex flex-col items-center gap-2 shadow-md">
          <span class="text-4xl">🥇</span>
          <div class="w-14 h-14 rounded-full bg-amber-200 text-amber-800 flex items-center justify-center text-base font-bold">
            {{ initials(ranking[0]?.name) }}
          </div>
          <p class="text-sm font-semibold text-slate-900 truncate w-full text-center">{{ ranking[0]?.name?.split(' ')[0] }}</p>
          <p class="text-lg font-bold text-blue-700 font-mono">{{ formatCurrency(ranking[0]?.total_company_savings) }}</p>
          <p class="text-xs text-slate-500">p/ empresa</p>
          <span class="text-xs bg-amber-200 text-amber-800 px-2 py-0.5 rounded-full font-semibold">Campeão</span>
        </div>

        <!-- 3rd place (right) -->
        <div class="bg-orange-50 border-2 border-orange-200 rounded-2xl p-4 text-center flex flex-col items-center gap-2 mt-6">
          <span class="text-3xl">🥉</span>
          <div class="w-12 h-12 rounded-full bg-orange-200 text-orange-800 flex items-center justify-center text-sm font-bold">
            {{ initials(ranking[2]?.name) }}
          </div>
          <p class="text-sm font-semibold text-slate-800 truncate w-full text-center">{{ ranking[2]?.name?.split(' ')[0] }}</p>
          <p class="text-base font-bold text-blue-700 font-mono">{{ formatCurrency(ranking[2]?.total_company_savings) }}</p>
          <p class="text-xs text-slate-400">p/ empresa</p>
        </div>
      </div>

      <!-- Full ranking list -->
      <div v-if="!loading && ranking.length" class="space-y-3">
        <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wide px-1">Classificação completa</h2>
        <RankingRow
          v-for="entry in ranking"
          :key="entry.user_id"
          :position="entry.position"
          :name="entry.name"
          :position-title="entry.position_title"
          :department="entry.department"
          :total-company-savings="entry.total_company_savings"
          :total-onhappy-coins-earned="entry.total_onhappy_coins_earned"
          :total-bookings="entry.total_bookings"
        />
      </div>
    </div>
  </AppShell>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import AppShell from '../../layouts/AppShell.vue'
import RankingRow from '../../components/approver/RankingRow.vue'

const loading = ref(true)
const ranking = ref([])

function initials(name) {
  if (!name) return '?'
  return name.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase()
}

function formatCurrency(v) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v ?? 0)
}

onMounted(async () => {
  try {
    const res = await axios.get('/approver/ranking')
    ranking.value = res.data.ranking
  } catch (e) {
    console.error('Erro ao carregar ranking', e)
  } finally {
    loading.value = false
  }
})
</script>
