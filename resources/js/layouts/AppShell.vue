<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Header -->
    <TheHeader>
      <template #title>
        <span class="text-sm text-slate-600">{{ pageTitle }}</span>
      </template>
    </TheHeader>

    <!-- Body: sidebar + main -->
    <div class="flex pt-16 min-h-screen">
      <!-- Sidebar -->
      <TheSidebar :role="auth.role" />

      <!-- Main content — flex-1 ocupa todo o espaço restante -->
      <main class="flex-1 min-w-0">
        <div class="w-[95%] mx-auto py-6">
          <slot />
        </div>
      </main>
    </div>

    <!-- Mobile overlay (when sidebar is open) -->
    <!-- Mobile support: sidebar is always visible on desktop -->
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import TheHeader from '../components/layout/TheHeader.vue'
import TheSidebar from '../components/layout/TheSidebar.vue'

const auth = useAuthStore()
const route = useRoute()

const pageTitles = {
  'traveler.search': 'Hospedagens',
  'traveler.wallet': 'Minha Carteira',
  'approver.dashboard': 'Dashboard de Economia',
}

const pageTitle = computed(() => pageTitles[route.name] || '')
</script>
