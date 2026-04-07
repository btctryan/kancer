import { defineStore } from 'pinia'
import { router } from '@inertiajs/vue3'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('auth_user') || 'null'),
    token: localStorage.getItem('auth_token') || null,
  }),

  getters: {
    isLoggedIn: (state) => !!state.token,
  },

  actions: {
    // ─── HELPER: header untuk request ──────────────
    getHeaders(withAuth = false) {
      const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      }
      if (withAuth && this.token) {
        headers['Authorization'] = `Bearer ${this.token}`
      }
      return headers
    },

    // ─── REGISTER ───────────────────────────────────
    async register(name, email, phone, password, passwordConfirmation) {
      const response = await fetch('/api/register', {
        method: 'POST',
        headers: this.getHeaders(),
        body: JSON.stringify({
          name,
          email,
          phone,
          password,
          password_confirmation: passwordConfirmation,
        }),
      })

      const data = await response.json()

      if (!response.ok) {
        // Ambil pesan error dari Laravel validation
        if (data.errors) {
          const firstError = Object.values(data.errors)[0][0]
          throw new Error(firstError)
        }
        throw new Error(data.message || 'Registrasi gagal.')
      }

      this.user = data.user
      this.token = data.token
      localStorage.setItem('auth_user', JSON.stringify(data.user))
      localStorage.setItem('auth_token', data.token)
      router.visit('/')
    },

    // ─── LOGIN ───────────────────────────────────────
    async login(email, password) {
      const response = await fetch('/api/login', {
        method: 'POST',
        headers: this.getHeaders(),
        body: JSON.stringify({ email, password }),
      })

      const data = await response.json()

      if (!response.ok) {
        if (data.errors) {
          const firstError = Object.values(data.errors)[0][0]
          throw new Error(firstError)
        }
        throw new Error(data.message || 'Login gagal.')
      }

      this.user = data.user
      this.token = data.token
      localStorage.setItem('auth_user', JSON.stringify(data.user))
      localStorage.setItem('auth_token', data.token)
      router.visit('/')
    },

    // ─── LOGOUT ──────────────────────────────────────
    async logout() {
      try {
        await fetch('/api/logout', {
          method: 'POST',
          headers: this.getHeaders(true),
        })
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.user = null
        this.token = null
        localStorage.removeItem('auth_user')
        localStorage.removeItem('auth_token')
        router.visit('/login')
      }
    },

    // ─── FORGOT PASSWORD ─────────────────────────────
    async forgotPassword(email) {
      const response = await fetch('/api/forgot-password', {
        method: 'POST',
        headers: this.getHeaders(),
        body: JSON.stringify({ email }),
      })

      const data = await response.json()

      if (!response.ok) {
        if (data.errors) {
          const firstError = Object.values(data.errors)[0][0]
          throw new Error(firstError)
        }
        throw new Error(data.message || 'Gagal mengirim email reset.')
      }

      return true
    },

    // ─── AMBIL DATA USER (opsional) ──────────────────
    async fetchUser() {
      if (!this.token) return

      const response = await fetch('/api/user', {
        headers: this.getHeaders(true),
      })

      if (response.ok) {
        const data = await response.json()
        this.user = data.user
        localStorage.setItem('auth_user', JSON.stringify(data.user))
      } else {
        // Token tidak valid, logout otomatis
        this.logout()
      }
    },
  },
})