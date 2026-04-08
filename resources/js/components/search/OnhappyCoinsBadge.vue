<template>
  <div class="relative group">
    <!-- Main badge -->
    <div :class="[
      'flex items-center gap-3 px-4 py-3 rounded-xl border-2 transition-all duration-300',
      'bg-green-50 border-green-300',
      'animate-pulse',
    ]">
      <!-- Icon -->
      <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center shrink-0">
        <span class="text-green-600 text-lg">✦</span>
      </div>

      <!-- Content -->
      <div>
        <p class="text-xs font-semibold text-green-700 uppercase tracking-wide leading-tight">Escolha esta opção e ganhe</p>
        <p class="text-xl font-bold text-green-700 leading-tight tracking-tight" style="font-family: 'JetBrains Mono', monospace;">
          ✦ Ganhe {{ coinsAmount }}
          <span class="text-sm font-semibold"> onhappy coins</span>
        </p>
      </div>
    </div>

    <!-- Hover tooltip -->
    <div class="absolute bottom-full left-0 mb-2 w-64 bg-slate-900 text-white rounded-xl p-3 text-xs
                opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-10 shadow-xl">
      <p class="font-semibold text-green-400 mb-2">✦ Seus onhappy coins</p>
      <p class="text-slate-300">
        ✦ {{ coinsAmount }} onhappy coins serão creditados na sua carteira ao confirmar esta reserva.
      </p>
      <!-- Arrow -->
      <div class="absolute top-full left-4 w-0 h-0 border-l-4 border-r-4 border-t-4 border-l-transparent border-r-transparent border-t-slate-900"></div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  coinsAmount:   { type: Number, required: true },
  companyAmount: { type: Number, default: 0 },
})
</script>
