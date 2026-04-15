// src/stores/admin.js
import { defineStore } from 'pinia'
import axios from 'axios'

export const useAdminStore = defineStore('admin', {
  state: () => ({
    admin: JSON.parse(localStorage.getItem('admin')) || null,
    token: localStorage.getItem('admin_token') || null,
  }),

  getters: {
    isLoggedIn: (state) => !!state.token,
  },

  actions: {
    async login(email, password) {
      const res = await axios.post('/api/admin/login', { email, password })
      this.token = res.data.token
      this.admin = res.data.admin
      localStorage.setItem('admin_token', this.token)
      localStorage.setItem('admin', JSON.stringify(this.admin))
      axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
    },

    async logout() {
      await axios.post('/api/admin/logout')
      this.token = null
      this.admin = null
      localStorage.removeItem('admin_token')
      localStorage.removeItem('admin')
      delete axios.defaults.headers.common['Authorization']
    },

    loadToken() {
      if (this.token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
      }
    },

    // CRUD Produk
    async getProducts() {
      const res = await axios.get('/api/admin/products')
      return res.data
    },

    async getProduct(id) {
      const res = await axios.get(`/api/admin/products/${id}`)
      return res.data
    },

    async createProduct(formData) {
      const res = await axios.post('/api/admin/products', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      return res.data
    },

    async updateProduct(id, formData) {
      // Laravel tidak support PUT dengan FormData, pakai POST + _method
      formData.append('_method', 'PUT')
      const res = await axios.post(`/api/admin/products/${id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      return res.data
    },

    async deleteProduct(id) {
      await axios.delete(`/api/admin/products/${id}`)
    },
  },
})