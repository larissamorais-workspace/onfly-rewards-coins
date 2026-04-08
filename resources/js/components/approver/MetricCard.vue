<template>
  <div class="bg-white rounded-2xl border border-slate-100 p-5">
    <div class="flex items-center gap-3 mb-3">
      <div :class="['w-10 h-10 rounded-xl flex items-center justify-center shrink-0', iconBg]">
        <component :is="icon" class="w-5 h-5" :class="iconColor" />
      </div>
      <p class="text-sm text-slate-500 font-medium leading-tight">{{ label }}</p>
    </div>
    <p :class="['text-2xl font-bold font-mono leading-tight', valueColor]">{{ displayValue }}</p>
    <p v-if="subtitle" class="text-xs text-slate-400 mt-1">{{ subtitle }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label:      { type: String, required: true },
  value:      { type: Number, default: 0 },
  format:     { type: String, default: 'number', validator: v => ['currency', 'number'].includes(v) },
  icon:       { type: [Object, Function], required: true },
  iconBg:     { type: String, default: 'bg-blue-50' },
  iconColor:  { type: String, default: 'text-blue-600' },
  valueColor: { type: String, default: 'text-slate-900' },
  subtitle:   { type: String, default: '' },
  suffix:     { type: String, default: '' },
})

const displayValue = computed(() => {
  if (props.format === 'currency') {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(props.value)
  }
  return props.value.toString() + props.suffix
})
</script>
