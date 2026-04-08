<template>
  <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm">
    <h2 class="text-lg font-semibold text-slate-900 mb-5" style="font-family: var(--font-display); letter-spacing: -0.02em;">Hospedagens</h2>

    <form @submit.prevent="handleSearch" class="space-y-4">
      <!-- Destination -->
      <CityAutocomplete
        :city-value="store.form.destination_city"
        :state-value="store.form.destination_state"
        @update:city-value="store.form.destination_city = $event"
        @update:state-value="store.form.destination_state = $event"
      />

      <!-- Dates -->
      <div class="grid grid-cols-2 gap-3">
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-medium text-slate-700">Check-in</label>
          <input
            v-model="store.form.check_in"
            type="date"
            required
            :min="today"
            class="h-10 px-3 text-sm text-slate-900 bg-slate-50 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-300 transition-all"
          />
        </div>
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-medium text-slate-700">Check-out</label>
          <input
            v-model="store.form.check_out"
            type="date"
            :min="store.form.check_in || today"
            class="h-10 px-3 text-sm text-slate-900 bg-slate-50 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-300 transition-all"
          />
        </div>
      </div>

      <!-- Error -->
      <p v-if="store.error" class="text-sm text-red-600 bg-red-50 px-3 py-2 rounded-lg">{{ store.error }}</p>

      <!-- Submit -->
      <button
        type="submit"
        :disabled="store.loading"
        class="w-full h-11 flex items-center justify-center gap-2 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 active:bg-blue-800 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
      >
        <svg v-if="store.loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 6.477 0 12h4zm2 9.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
        </svg>
        <Search v-else class="w-4 h-4" />
        {{ store.loading ? 'Buscando...' : 'Buscar hospedagens' }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { Search } from 'lucide-vue-next'
import { useSearchStore } from '../../stores/search'
import CityAutocomplete from './CityAutocomplete.vue'

const store = useSearchStore()

const today = new Date().toISOString().split('T')[0]

function handleSearch() {
  store.search()
}
</script>
