<template>
  <!-- Desktop sidebar -->
  <aside class="w-60 shrink-0 sticky top-0 h-[calc(100vh-4rem)] bg-white border-r border-slate-200 flex flex-col py-4 z-40 overflow-y-auto">
    <!-- Brand accent bar -->
    <div class="mx-3 mb-3 h-0.5 rounded-full bg-gradient-to-r from-blue-600 to-blue-400 opacity-60" />

    <!-- Nav items -->
    <nav class="flex-1 px-3 space-y-1">
      <RouterLink
        v-for="item in navItems"
        :key="item.to"
        :to="item.to"
        :class="[
          'flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] font-medium tracking-[0.01em] transition-all duration-150',
          'group',
          isActive(item.to)
            ? 'bg-blue-50 text-blue-700 border-l-[3px] border-blue-600 pl-[calc(0.75rem-3px)]'
            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
        ]"
      >
        <component :is="item.icon" :class="['w-5 h-5 shrink-0', isActive(item.to) ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600']" />
        {{ item.label }}
      </RouterLink>
    </nav>

    <!-- Footer -->
    <div class="px-4 pt-3 border-t border-slate-100">
      <p class="text-xs text-slate-400">Onfly Rewards</p>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { Search, Wallet, BarChart2 } from 'lucide-vue-next'

const props = defineProps({
  role: {
    type: String,
    required: true,
  }
})

const route = useRoute()

const travelerItems = [
  { to: '/traveler/search', label: 'Hospedagens', icon: Search },
  { to: '/traveler/wallet', label: 'Minha Carteira', icon: Wallet },
]

const approverItems = [
  { to: '/approver/dashboard', label: 'Dashboard de Economia', icon: BarChart2 },
]

const navItems = computed(() => props.role === 'traveler' ? travelerItems : approverItems)

function isActive(path) {
  return route.path === path || route.path.startsWith(path + '/')
}
</script>
