<template>
  <AppShell>
    <div class="w-full space-y-8">
      <!-- Header -->
      <div>
        <h1 class="text-2xl md:text-3xl font-bold text-slate-900" style="font-family: var(--font-display); letter-spacing: -0.025em;">Relatório de Economias</h1>
        <p class="text-sm text-slate-500 mt-1">Visibilidade total sobre o impacto do Onfly Rewards</p>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="space-y-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div v-for="i in 4" :key="i" class="bg-white rounded-2xl border border-slate-100 p-5 animate-pulse h-28"></div>
        </div>
      </div>

      <template v-else>
        <!-- ═══ SEÇÃO 1: KPIs Hero ═══ -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <MetricCard
            label="Economia Total da Empresa"
            :value="data.total_company_savings"
            format="currency"
            :icon="TrendingUp"
            icon-bg="bg-blue-50"
            icon-color="text-blue-600"
            value-color="text-blue-700"
          />
          <MetricCard
            label="Onhappy Coins Distribuídos"
            :value="data.total_onhappy_coins_distributed"
            format="currency"
            :icon="Coins"
            icon-bg="bg-amber-50"
            icon-color="text-amber-600"
            value-color="text-amber-700"
          />
          <MetricCard
            label="Reservas com Economia"
            :value="data.total_bookings_with_savings"
            format="number"
            :icon="Building2"
            icon-bg="bg-green-50"
            icon-color="text-green-600"
            value-color="text-slate-900"
          />
        </div>

        <!-- ═══ SEÇÃO 2: Engajamento por Departamento ═══ -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6">
          <h2 class="text-base font-semibold text-slate-900 mb-4" style="font-family: var(--font-display);">Engajamento por Departamento</h2>

          <div v-if="data.by_department?.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <div
              v-for="dept in data.by_department"
              :key="dept.department"
              class="rounded-xl border border-slate-100 p-4 hover:border-blue-200 transition-colors"
            >
              <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-semibold text-slate-800">{{ dept.department }}</p>
                <span class="text-xs text-slate-400">{{ dept.travelers_count }} viajante{{ dept.travelers_count !== 1 ? 's' : '' }}</span>
              </div>
              <div class="flex items-end justify-between">
                <div>
                  <p class="text-xs text-slate-500 mb-0.5">Economia gerada</p>
                  <p class="text-lg font-bold text-blue-700 font-mono">{{ formatCurrency(dept.total_savings) }}</p>
                </div>
                <div class="text-right">
                  <p class="text-xs text-slate-500 mb-0.5">Reservas</p>
                  <p class="text-lg font-bold text-slate-700">{{ dept.bookings_count }}</p>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-8 text-slate-400 text-sm">
            Nenhum departamento com reservas ainda.
          </div>
        </div>

        <!-- ═══ SEÇÃO 4: Relatório Detalhado ═══ -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-slate-900" style="font-family: var(--font-display);">Relatório de Reservas</h2>
            <span class="text-xs text-slate-400">{{ data.bookings_report?.length ?? 0 }} registro{{ (data.bookings_report?.length ?? 0) !== 1 ? 's' : '' }}</span>
          </div>

          <div v-if="data.bookings_report?.length" class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="border-b border-slate-200">
                  <th class="text-left py-3 px-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Colaborador</th>
                  <th class="text-left py-3 px-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Depto</th>
                  <th class="text-left py-3 px-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Hotel</th>
                  <th class="text-left py-3 px-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Destino</th>
                  <th class="text-right py-3 px-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Pago</th>
                  <th class="text-right py-3 px-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Economia</th>
                  <th class="text-right py-3 px-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Coins</th>
                  <th class="text-center py-3 px-2 text-xs font-semibold text-slate-500 uppercase tracking-wide">Data</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="b in data.bookings_report"
                  :key="b.id"
                  class="border-b border-slate-50 hover:bg-slate-50 transition-colors"
                >
                  <td class="py-3 px-2 font-medium text-slate-800">{{ b.traveler_name }}</td>
                  <td class="py-3 px-2 text-slate-500">{{ b.department }}</td>
                  <td class="py-3 px-2 text-slate-700">{{ b.provider_name }}</td>
                  <td class="py-3 px-2 text-slate-500">{{ b.destination }}</td>
                  <td class="py-3 px-2 text-right font-mono text-slate-700">{{ formatCurrency(b.paid_price) }}</td>
                  <td class="py-3 px-2 text-right font-mono text-blue-700 font-semibold">{{ formatCurrency(b.company_savings) }}</td>
                  <td class="py-3 px-2 text-right font-mono text-green-700 font-semibold">{{ b.onhappy_coins_amount }}</td>
                  <td class="py-3 px-2 text-center text-slate-400">{{ b.check_in }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-else class="text-center py-8 text-slate-400 text-sm">
            Nenhuma reserva registrada ainda.
          </div>
        </div>
      </template>
    </div>
  </AppShell>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { TrendingUp, Coins, Building2 } from 'lucide-vue-next'
import AppShell from '../../layouts/AppShell.vue'
import MetricCard from '../../components/approver/MetricCard.vue'

const loading = ref(true)
const data = ref({
  total_company_savings: 0,
  total_onhappy_coins_distributed: 0,
  total_savings_bruto: 0,
  total_bookings_with_savings: 0,
  active_travelers: 0,
  by_department: [],
  bookings_report: [],
})


function formatCurrency(v) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v ?? 0)
}

onMounted(async () => {
  try {
    const res = await axios.get('/approver/dashboard')
    data.value = res.data
  } catch (e) {
    console.error('Erro ao carregar dashboard', e)
  } finally {
    loading.value = false
  }
})
</script>
