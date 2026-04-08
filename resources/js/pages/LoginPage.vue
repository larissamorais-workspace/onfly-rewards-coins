<template>
  <div class="min-h-screen bg-slate-50 flex items-center justify-center px-4">
    <div class="w-full max-w-xl">
      <!-- Logo -->
      <div class="text-center mb-8">
        <img :src="'/onfly-logo.svg'" alt="Onfly Rewards" class="h-10 w-auto mx-auto" style="margin-bottom: 2px;" />
        <h1 class="text-3xl font-bold" style="font-family: var(--font-display); letter-spacing: -0.03em; color: #009efb;">Rewards</h1>
      </div>

      <!-- Card -->
      <div class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm">
        <form @submit.prevent="handleLogin" class="space-y-6">
          <!-- Name -->
          <div class="flex flex-col gap-1.5">
            <label for="name" class="text-sm font-medium text-slate-700">Como você se chama?</label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              placeholder="Seu nome"
              required
              :disabled="loading"
              class="w-full h-11 px-4 text-sm text-slate-900 bg-slate-50 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 transition-all disabled:opacity-50"
            />
          </div>

          <!-- Role selector -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-slate-700">Selecione seu perfil</label>
            <div class="grid grid-cols-2 gap-3">
              <button
                type="button"
                @click="form.role = 'traveler'"
                :class="[
                  'flex flex-col items-center gap-2 py-5 rounded-xl border-2 transition-all duration-150 cursor-pointer',
                  form.role === 'traveler'
                    ? 'border-blue-500 bg-blue-50 text-blue-700 shadow-sm'
                    : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300 hover:bg-slate-50'
                ]"
              >
                <Hotel class="w-7 h-7" />
                <span class="text-sm font-semibold">Viajante</span>
                <span class="text-[11px] text-slate-400">Buscar hotéis e ganhar coins</span>
              </button>

              <button
                type="button"
                @click="form.role = 'approver'"
                :class="[
                  'flex flex-col items-center gap-2 py-5 rounded-xl border-2 transition-all duration-150 cursor-pointer',
                  form.role === 'approver'
                    ? 'border-blue-500 bg-blue-50 text-blue-700 shadow-sm'
                    : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300 hover:bg-slate-50'
                ]"
              >
                <BarChart2 class="w-7 h-7" />
                <span class="text-sm font-semibold">Aprovador</span>
                <span class="text-[11px] text-slate-400">Dashboard de economia</span>
              </button>
            </div>
          </div>

          <!-- Error -->
          <div v-if="error" class="flex items-center gap-2 p-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
            {{ error }}
          </div>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="loading || !form.name || !form.role"
            class="w-full h-11 flex items-center justify-center gap-2 text-white text-sm font-semibold rounded-xl transition-all disabled:opacity-40 disabled:cursor-not-allowed"
            style="background-color: #009efb;"
            @mouseover="$event.currentTarget.style.backgroundColor='#0089d9'"
            @mouseleave="$event.currentTarget.style.backgroundColor='#009efb'"
          >
            <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 6.477 0 12h4zm2 9.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
            </svg>
            {{ loading ? 'Entrando...' : 'Entrar' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Hotel, BarChart2 } from 'lucide-vue-next'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()

const form = ref({ name: '', role: '' })
const loading = ref(false)
const error = ref('')

async function handleLogin() {
  error.value = ''
  loading.value = true

  try {
    const user = await auth.login(form.value.name, form.value.role)

    if (user.role === 'traveler') {
      router.push('/traveler/search')
    } else {
      router.push('/approver/dashboard')
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Erro ao entrar. Tente novamente.'
  } finally {
    loading.value = false
  }
}
</script>
