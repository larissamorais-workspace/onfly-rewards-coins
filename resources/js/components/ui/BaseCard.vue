<template>
  <div
    :class="[
      'bg-white rounded-xl transition-shadow duration-150',
      'border border-slate-100',
      paddingClasses,
      hoverable ? 'hover:shadow-md cursor-pointer' : '',
    ]"
  >
    <slot />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  padding: {
    type: String,
    default: 'default',
    validator: (v) => ['default', 'compact', 'none'].includes(v)
  },
  hoverable: { type: Boolean, default: false },
})

const paddingClasses = computed(() => ({
  default: 'p-5',
  compact: 'p-3',
  none: 'p-0',
}[props.padding]))
</script>
