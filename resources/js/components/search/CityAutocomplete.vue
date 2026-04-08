<template>
  <div class="flex flex-col gap-1.5 relative">
    <label class="text-sm font-medium text-slate-700">Cidade de destino</label>

    <div class="relative">
      <input
        ref="inputRef"
        v-model="query"
        type="text"
        :placeholder="ibgeLoading ? 'Carregando cidades...' : 'Digite a cidade de destino...'"
        :disabled="ibgeLoading"
        autocomplete="off"
        required
        class="w-full h-10 px-3 pr-8 text-sm text-slate-900 bg-slate-50 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 transition-all disabled:opacity-50 disabled:cursor-wait"
        @input="onInput"
        @keydown="onKeydown"
        @focus="showDropdown = suggestions.length > 0"
        @blur="onBlur"
      />

      <!-- Loading spinner -->
      <div v-if="ibgeLoading" class="absolute right-2.5 top-1/2 -translate-y-1/2">
        <svg class="animate-spin h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 6.477 0 12h4zm2 9.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
        </svg>
      </div>

      <!-- Clear button -->
      <button
        v-else-if="query"
        type="button"
        tabindex="-1"
        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
        @mousedown.prevent="clearSelection"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- Selected state badge -->
    <div v-if="stateValue && !showDropdown" class="flex items-center gap-1.5 mt-0.5">
      <span class="inline-flex items-center gap-1 text-xs text-blue-700 bg-blue-50 border border-blue-200 rounded-md px-2 py-0.5 font-medium">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        {{ stateValue }}
      </span>
    </div>

    <!-- Dropdown -->
    <ul
      v-if="showDropdown && suggestions.length > 0"
      class="absolute top-full mt-1 left-0 right-0 z-50 bg-white border border-slate-200 rounded-xl shadow-lg max-h-60 overflow-y-auto"
    >
      <li
        v-for="(city, i) in suggestions"
        :key="city['municipio-id']"
        :class="[
          'flex items-center justify-between px-3 py-2.5 cursor-pointer text-sm transition-colors',
          i === activeIndex ? 'bg-blue-50 text-blue-900' : 'text-slate-700 hover:bg-slate-50'
        ]"
        @mousedown.prevent="selectCity(city)"
      >
        <span class="font-medium">{{ city['municipio-nome'] }}</span>
        <span :class="['text-xs font-semibold px-1.5 py-0.5 rounded', i === activeIndex ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500']">
          {{ city['UF-sigla'] }}
        </span>
      </li>
    </ul>

    <!-- No results -->
    <div
      v-else-if="showDropdown && query.length >= 2 && !ibgeLoading"
      class="absolute top-full mt-1 left-0 right-0 z-50 bg-white border border-slate-200 rounded-xl shadow-lg px-4 py-3 text-sm text-slate-500 text-center"
    >
      Nenhuma cidade encontrada para "{{ query }}"
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
  cityValue:  { type: String, default: '' },
  stateValue: { type: String, default: '' },
})

const emit = defineEmits(['update:cityValue', 'update:stateValue'])

const inputRef    = ref(null)
const query       = ref(props.cityValue)
const cities      = ref([])
const ibgeLoading = ref(true)
const showDropdown = ref(false)
const activeIndex  = ref(-1)

function normalize(str) {
  return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase()
}

const suggestions = computed(() => {
  if (!query.value || query.value.length < 2) return []
  const q = normalize(query.value)
  return cities.value
    .filter(c => normalize(c['municipio-nome']).includes(q))
    .slice(0, 10)
})

onMounted(async () => {
  try {
    const { data } = await axios.get(
      'https://servicodados.ibge.gov.br/api/v1/localidades/municipios?view=nivelado',
      { withCredentials: false }
    )
    cities.value = data
  } catch {
    // silently degrade — input stays enabled as plain text
  } finally {
    ibgeLoading.value = false
  }
})

function onInput() {
  activeIndex.value = -1
  showDropdown.value = suggestions.value.length > 0 || query.value.length >= 2
  // Clear state if user edits the query manually
  if (props.stateValue) {
    emit('update:cityValue', '')
    emit('update:stateValue', '')
  }
}

function selectCity(city) {
  query.value = city['municipio-nome']
  emit('update:cityValue', city['municipio-nome'])
  emit('update:stateValue', city['UF-sigla'])
  showDropdown.value = false
  activeIndex.value = -1
}

function clearSelection() {
  query.value = ''
  emit('update:cityValue', '')
  emit('update:stateValue', '')
  showDropdown.value = false
  activeIndex.value = -1
  inputRef.value?.focus()
}

function onKeydown(e) {
  if (!showDropdown.value || suggestions.value.length === 0) return

  if (e.key === 'ArrowDown') {
    e.preventDefault()
    activeIndex.value = Math.min(activeIndex.value + 1, suggestions.value.length - 1)
  } else if (e.key === 'ArrowUp') {
    e.preventDefault()
    activeIndex.value = Math.max(activeIndex.value - 1, 0)
  } else if (e.key === 'Enter') {
    if (activeIndex.value >= 0) {
      e.preventDefault()
      selectCity(suggestions.value[activeIndex.value])
    }
  } else if (e.key === 'Escape') {
    showDropdown.value = false
    activeIndex.value = -1
  }
}

function onBlur() {
  // Delay to allow mousedown on dropdown items to fire first
  setTimeout(() => {
    showDropdown.value = false
    activeIndex.value = -1
  }, 150)
}
</script>
