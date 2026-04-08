<template>
  <AppShell>
    <div class="w-full space-y-6">
      <!-- Search Form -->
      <SearchForm />

      <!-- Loading skeleton -->
      <div v-if="search.loading" class="space-y-4">
        <div v-for="i in 4" :key="i" class="bg-white rounded-2xl border border-slate-100 p-5 animate-pulse">
          <div class="flex justify-between items-start mb-4">
            <div class="space-y-2 flex-1">
              <div class="h-5 bg-slate-200 rounded w-2/3"></div>
              <div class="h-4 bg-slate-100 rounded w-1/3"></div>
            </div>
            <div class="h-8 bg-slate-200 rounded w-24"></div>
          </div>
          <div class="flex gap-2">
            <div class="h-6 bg-slate-100 rounded-full w-16"></div>
            <div class="h-6 bg-slate-100 rounded-full w-20"></div>
          </div>
        </div>
      </div>

      <!-- Results -->
      <div v-if="search.hasResults && !search.loading" class="space-y-4">
        <div class="flex items-center justify-between">
          <p class="text-sm font-medium text-slate-600">
            {{ search.results.length }} opções encontradas para
            <span class="font-semibold text-slate-900">{{ search.destination }}</span>
          </p>
          <span v-if="search.onhappyCoinsResults.length" class="text-xs text-green-700 bg-green-50 border border-green-200 px-2.5 py-1 rounded-full font-medium">
            ✦ {{ search.onhappyCoinsResults.length }} com onhappy coins
          </span>
        </div>

        <div class="space-y-4">
          <ResultCard
            v-for="result in search.results"
            :key="result.id"
            :result="result"
            :policy="search.policy"
            @select="search.selectResult(result)"
          />
        </div>
      </div>

      <!-- Empty state after search -->
      <div v-else-if="!search.loading && search.destination && !search.hasResults"
           class="text-center py-16">
        <div class="text-5xl mb-4">🔍</div>
        <h3 class="text-lg font-semibold text-slate-700 mb-2" style="font-family: var(--font-display); letter-spacing: -0.025em;">Nenhuma opção encontrada</h3>
        <p class="text-sm text-slate-500">Tente outro destino ou outras datas.</p>
      </div>

    </div>

    <!-- Booking Modal -->
    <BookingModal
      v-if="search.showConfirmModal && search.selectedResult"
      :result="search.selectedResult"
      :checkin="search.form.check_in"
      :checkout="search.form.check_out"
      :loading="search.bookingLoading"
      @confirm="handleConfirm"
      @cancel="search.closeModal()"
    />

    <!-- Success Toast -->
    <Transition name="toast">
      <div v-if="showToast"
           class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-4 bg-green-700 text-white rounded-2xl shadow-2xl max-w-sm">
        <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center text-xl shrink-0">🎉</div>
        <div>
          <p class="font-semibold text-sm">Reserva confirmada!</p>
          <p class="text-green-200 text-xs font-mono">{{ toastMessage }}</p>
        </div>
      </div>
    </Transition>
  </AppShell>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import AppShell from '../../layouts/AppShell.vue'
import SearchForm from '../../components/search/SearchForm.vue'
import ResultCard from '../../components/search/ResultCard.vue'
import BookingModal from '../../components/search/BookingModal.vue'
import { useSearchStore } from '../../stores/search'

const search = useSearchStore()
const router = useRouter()

const showToast = ref(false)
const toastMessage = ref('')

async function handleConfirm() {
  try {
    const data = await search.confirmBooking()
    const coinsEarned = data.onhappy_coins_earned

    if (coinsEarned > 0) {
      toastMessage.value = `${coinsEarned} onhappy coins adicionados à sua carteira!`
    } else {
      toastMessage.value = 'Reserva realizada com sucesso!'
    }

    showToast.value = true
    setTimeout(() => {
      showToast.value = false
      router.push('/traveler/wallet')
    }, 2500)
  } catch (e) {
    alert('Erro ao confirmar reserva. Tente novamente.')
  }
}
</script>

<style scoped>
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(1rem); }
</style>
