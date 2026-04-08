<template>
  <span
    :class="[
      'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold',
      variantClasses,
      pulse ? 'animate-pulse' : '',
    ]"
  >
    <slot />
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'neutral',
    validator: (v) => ['reward', 'gold', 'brand', 'neutral', 'danger'].includes(v)
  },
  pulse: { type: Boolean, default: false },
})

const variantClasses = computed(() => ({
  reward: 'bg-green-50 text-green-700 border border-green-200',
  gold: 'bg-amber-50 text-amber-700 border border-amber-200',
  brand: 'bg-blue-50 text-blue-700 border border-blue-200',
  neutral: 'bg-slate-100 text-slate-600 border border-slate-200',
  danger: 'bg-red-50 text-red-700 border border-red-200',
}[props.variant]))
</script>
