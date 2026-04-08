import { defineStore } from 'pinia'
import axios from 'axios'

export const useSearchStore = defineStore('search', {
  state: () => ({
    form: {
      destination_city: '',
      destination_state: '',
      check_in: '',
      check_out: '',
    },
    results: [],
    policy: null,
    destination: '',
    loading: false,
    error: null,
    selectedResult: null,
    showConfirmModal: false,
    bookingLoading: false,
    lastBooking: null,
  }),

  getters: {
    hasResults: (state) => state.results.length > 0,
    onhappyCoinsResults: (state) => state.results.filter((r) => r.has_onhappy_coins),
  },

  actions: {
    async search() {
      this.loading = true
      this.error = null
      this.results = []
      this.policy = null

      try {
        const params = { ...this.form }
        const response = await axios.get('/traveler/search', { params })
        this.results = response.data.results
        this.policy = response.data.policy
        this.destination = response.data.destination
      } catch (e) {
        this.error = e.response?.data?.message || 'Erro ao buscar resultados.'
      } finally {
        this.loading = false
      }
    },

    selectResult(result) {
      this.selectedResult = result
      this.showConfirmModal = true
    },

    closeModal() {
      this.showConfirmModal = false
      this.selectedResult = null
    },

    async confirmBooking() {
      if (!this.selectedResult) return null

      this.bookingLoading = true
      try {
        const r = this.selectedResult
        const payload = {
          modal: 'hotel',
          provider_name: r.name,
          destination_city: this.form.destination_city,
          destination_state: this.form.destination_state,
          paid_price: r.price,
          original_price: r.original_price ?? r.price,
          check_in: this.form.check_in,
          check_out: this.form.check_out || null,
          travel_policy_id: this.policy?.id ?? null,
        }

        const response = await axios.post('/traveler/bookings', payload)
        this.lastBooking = response.data
        this.showConfirmModal = false
        this.selectedResult = null
        return response.data
      } catch (e) {
        throw e
      } finally {
        this.bookingLoading = false
      }
    },

    reset() {
      this.results = []
      this.policy = null
      this.destination = ''
      this.error = null
      this.selectedResult = null
      this.showConfirmModal = false
      this.lastBooking = null
    },
  },
})
