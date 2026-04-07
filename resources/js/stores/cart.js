import { defineStore } from 'pinia'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    total: 0,
    loading: false,
  }),

  getters: {
    totalItems: (state) => state.items.reduce((sum, item) => sum + item.quantity, 0),
    totalPrice: (state) => state.total,
  },

  actions: {
    // ─── HELPER: ambil token dari localStorage ──────
    getHeaders() {
      const token = localStorage.getItem('auth_token')
      return {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
      }
    },

    // ─── AMBIL SEMUA ISI CART DARI API ──────────────
    async fetchCart() {
      this.loading = true
      try {
        const response = await fetch('/api/cart', {
          headers: this.getHeaders(),
        })
        if (response.ok) {
          const data = await response.json()
          this.items = data.items
          this.total = data.total
        }
      } catch (error) {
        console.error('Gagal memuat cart:', error)
      } finally {
        this.loading = false
      }
    },

    // ─── TAMBAH PRODUK KE CART ───────────────────────
    async add(product, quantity = 1) {
      try {
        const response = await fetch('/api/cart', {
          method: 'POST',
          headers: this.getHeaders(),
          body: JSON.stringify({
            product_id: product.id,
            quantity,
          }),
        })

        const data = await response.json()

        if (!response.ok) {
          throw new Error(data.message || 'Gagal menambahkan ke keranjang.')
        }

        // Refresh cart dari API
        await this.fetchCart()
        return true
      } catch (error) {
        throw error
      }
    },

    // ─── UPDATE QUANTITY ─────────────────────────────
    async updateQty(cartId, quantity) {
      try {
        const response = await fetch(`/api/cart/${cartId}`, {
          method: 'PUT',
          headers: this.getHeaders(),
          body: JSON.stringify({ quantity }),
        })

        const data = await response.json()

        if (!response.ok) {
          throw new Error(data.message || 'Gagal mengubah jumlah.')
        }

        await this.fetchCart()
        return true
      } catch (error) {
        throw error
      }
    },

    // ─── HAPUS SATU ITEM ─────────────────────────────
    async remove(cartId) {
      try {
        const response = await fetch(`/api/cart/${cartId}`, {
          method: 'DELETE',
          headers: this.getHeaders(),
        })

        if (!response.ok) {
          throw new Error('Gagal menghapus produk.')
        }

        await this.fetchCart()
        return true
      } catch (error) {
        throw error
      }
    },

    // ─── KOSONGKAN SEMUA CART ────────────────────────
    async clear() {
      try {
        const response = await fetch('/api/cart', {
          method: 'DELETE',
          headers: this.getHeaders(),
        })

        if (!response.ok) {
          throw new Error('Gagal mengosongkan keranjang.')
        }

        this.items = []
        this.total = 0
        return true
      } catch (error) {
        throw error
      }
    },

    // ─── GENERATE PESAN WA ───────────────────────────
    generateWAMessage() {
      const user = JSON.parse(localStorage.getItem('auth_user') || '{}')

      let pesan = `Halo THE KANCERS! \n\n`
      pesan += `Saya ingin memesan domba berikut:\n\n`
      pesan += `DETAIL PESANAN:\n`

      this.items.forEach((item, index) => {
        pesan += `${index + 1}. ${item.product.name}\n`
        pesan += `   Jumlah : ${item.quantity} ekor\n`
        pesan += `   Harga  : ${this.formatRupiah(item.product.price)}/ekor\n`
        pesan += `   Subtotal: ${this.formatRupiah(item.subtotal)}\n\n`
      })

      pesan += ` Total: ${this.formatRupiah(this.total)}\n\n`
      pesan += ` DETAIL AKUN:\n`
      pesan += `Nama  : ${user.name || '-'}\n`
      pesan += `Email : ${user.email || '-'}\n`
      pesan += `HP    : +62${user.phone || '-'}\n\n`
      pesan += `Mohon konfirmasi ketersediaan dan jadwal pengiriman.\n`
      pesan += `Terima kasih! 🙏`

      return pesan
    },

    // ─── CHECKOUT VIA WHATSAPP ───────────────────────
    checkoutViaWA() {
      const pesan = this.generateWAMessage()
      const encoded = encodeURIComponent(pesan)
      const waNumber = '6282315000834'
      window.open(`https://wa.me/${waNumber}?text=${encoded}`, '_blank')
    },

    // ─── HELPER FORMAT RUPIAH ────────────────────────
    formatRupiah(value) {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
      }).format(value)
    },
  },
})