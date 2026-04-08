import { defineStore } from 'pinia'
import axios from 'axios'

axios.defaults.baseURL = '/api'
axios.defaults.headers.common['Accept'] = 'application/json'
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// Restore token from localStorage on page load
const storedToken = localStorage.getItem('auth_token')
if (storedToken) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${storedToken}`
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    isAuthenticated: !!storedToken,
    role: localStorage.getItem('user_role') || null,
    checked: false,
  }),

  getters: {
    isTraveler: (state) => state.role === 'traveler',
    isApprover: (state) => state.role === 'approver',
  },

  actions: {
    async login(name, role) {
      const response = await axios.post('/login', { name, role })
      const { token, user } = response.data

      // Store token and set as default header
      localStorage.setItem('auth_token', token)
      localStorage.setItem('user_role', user.role)
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

      this.user = user
      this.isAuthenticated = true
      this.role = user.role
      this.checked = true

      return user
    },

    async logout() {
      try {
        await axios.post('/logout')
      } finally {
        localStorage.removeItem('auth_token')
        localStorage.removeItem('user_role')
        delete axios.defaults.headers.common['Authorization']

        this.user = null
        this.isAuthenticated = false
        this.role = null
        this.checked = true
      }
    },

    async fetchUser() {
      const token = localStorage.getItem('auth_token')
      if (!token) {
        this.checked = true
        return
      }

      try {
        const response = await axios.get('/user')
        const { user } = response.data

        this.user = user
        this.isAuthenticated = true
        this.role = user.role
        localStorage.setItem('user_role', user.role)
      } catch {
        localStorage.removeItem('auth_token')
        localStorage.removeItem('user_role')
        delete axios.defaults.headers.common['Authorization']

        this.user = null
        this.isAuthenticated = false
        this.role = null
      } finally {
        this.checked = true
      }
    },
  },
})
