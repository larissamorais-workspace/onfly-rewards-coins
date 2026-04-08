<template>
  <header class="fixed top-0 left-0 right-0 z-50 h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6">
    <!-- Left: Logo + page title slot -->
    <div class="flex items-center gap-3">
      <div class="flex items-center">
        <img :src="'/onfly-logo.svg'" alt="Onfly Rewards" class="h-7 w-auto" />
      </div>
    </div>

    <!-- Right: User info + logout -->
    <div class="flex items-center gap-3">
      <div class="text-right hidden sm:block">
        <p class="text-sm font-medium text-slate-800 leading-tight">{{ user?.name }}</p>
        <p class="text-xs text-slate-500 leading-tight">{{ user?.position || user?.role }}</p>
      </div>

      <!-- Logout button -->
      <button
        @click="handleLogout"
        :disabled="loggingOut"
        class="flex items-center gap-1.5 px-3 py-1.5 text-xs text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-all duration-150 disabled:opacity-50"
        title="Sair"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
        <span class="hidden sm:inline">Sair</span>
      </button>
    </div>
  </header>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const auth = useAuthStore()
const router = useRouter()
const loggingOut = ref(false)

const user = computed(() => auth.user)
const initials = computed(() => {
  if (!auth.user?.name) return '?'
  return auth.user.name.split(' ').slice(0, 2).map(n => n[0]).join('').toUpperCase()
})

async function handleLogout() {
  loggingOut.value = true
  try {
    await auth.logout()
    router.push('/login')
  } finally {
    loggingOut.value = false
  }
}
</script>
