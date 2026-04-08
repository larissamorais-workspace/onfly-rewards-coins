<template>
  <div class="flex flex-col gap-1.5">
    <label
      v-if="label"
      :for="inputId"
      class="text-sm font-medium text-slate-700"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 ml-0.5">*</span>
    </label>

    <input
      :id="inputId"
      v-bind="$attrs"
      :type="type"
      :disabled="disabled"
      :value="modelValue"
      :placeholder="placeholder"
      :class="[
        'w-full h-10 px-3 text-sm text-slate-900 bg-slate-50 rounded-lg transition-all duration-150',
        'border focus:outline-none focus:ring-2 focus:ring-offset-0',
        error
          ? 'border-red-400 focus:ring-red-300'
          : 'border-slate-200 focus:ring-blue-300 focus:border-blue-400',
        disabled ? 'opacity-50 cursor-not-allowed' : '',
      ]"
      @input="$emit('update:modelValue', $event.target.value)"
    />

    <p v-if="error" class="text-xs text-red-600 flex items-center gap-1">
      <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
      </svg>
      {{ error }}
    </p>

    <p v-else-if="helper" class="text-xs text-slate-500">{{ helper }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: String, default: '' },
  label: { type: String, default: '' },
  type: { type: String, default: 'text' },
  placeholder: { type: String, default: '' },
  error: { type: String, default: '' },
  helper: { type: String, default: '' },
  disabled: { type: Boolean, default: false },
  required: { type: Boolean, default: false },
})

defineEmits(['update:modelValue'])

const inputId = computed(() => `input-${Math.random().toString(36).slice(2, 9)}`)
</script>
