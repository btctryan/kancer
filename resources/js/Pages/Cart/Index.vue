<template>
  <MainLayout>
    <section class="cart-page">

      <div class="cart-header">
        <h1 class="cart-title">Keranjang Belanja</h1>
        <p class="cart-sub" v-if="cartStore.items.length > 0">
          {{ cartStore.totalItems }} produk dipilih
        </p>
      </div>

      <!-- LOADING -->
      <div v-if="cartStore.loading" class="cart-loading">
        <div class="skeleton" v-for="n in 3" :key="n"></div>
      </div>

      <!-- KOSONG -->
      <div v-else-if="cartStore.items.length === 0" class="cart-empty">
        <div class="empty-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <path d="M16 10a4 4 0 01-8 0"/>
          </svg>
        </div>
        <h3 class="empty-title">Keranjang Kosong</h3>
        <p class="empty-desc">Belum ada produk yang ditambahkan ke keranjang.</p>
        <button class="btn-primary" @click="$inertia.visit('/products')">
          Lihat Produk
        </button>
      </div>

      <!-- ISI CART -->
      <div v-else class="cart-content">

        <!-- DAFTAR PRODUK -->
        <div class="cart-items">

          <div class="cart-item" v-for="item in cartStore.items" :key="item.id">
            <!-- Foto Produk -->
            <div class="item-img">
              <img v-if="item.product.image" :src="item.product.image" :alt="item.product.name" />
              <div v-else class="item-img-placeholder">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" width="28" height="28">
                  <path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                  <circle cx="12" cy="13" r="3"/>
                </svg>
              </div>
            </div>

            <!-- Info Produk -->
            <div class="item-info">
              <span class="item-badge" v-if="item.product.badge">{{ item.product.badge }}</span>
              <p class="item-name">{{ item.product.name }}</p>
              <p class="item-price">{{ formatRupiah(item.product.price) }} / ekor</p>
            </div>

            <!-- Quantity Control -->
            <div class="item-qty">
              <button
                class="qty-btn"
                @click="decreaseQty(item)"
                :disabled="item.quantity <= 1 || loadingItem === item.id"
              >−</button>
              <span class="qty-num">{{ item.quantity }}</span>
              <button
                class="qty-btn"
                @click="increaseQty(item)"
                :disabled="item.quantity >= item.product.stock || loadingItem === item.id"
              >+</button>
            </div>

            <!-- Subtotal -->
            <div class="item-subtotal">
              <p class="subtotal-label">Subtotal</p>
              <p class="subtotal-value">{{ formatRupiah(item.subtotal) }}</p>
            </div>

            <!-- Hapus -->
            <button
              class="item-remove"
              @click="removeItem(item.id)"
              :disabled="loadingItem === item.id"
              title="Hapus produk"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                <path d="M10 11v6M14 11v6"/>
                <path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
              </svg>
            </button>
          </div>

          <!-- Tombol kosongkan cart -->
          <div class="cart-actions">
            <button class="btn-clear" @click="confirmClear" :disabled="clearing">
              <span v-if="!clearing">Kosongkan Keranjang</span>
              <span v-else>Mengosongkan...</span>
            </button>
          </div>
        </div>

        <!-- RINGKASAN & CHECKOUT -->
        <div class="cart-summary">
          <h3 class="summary-title">Ringkasan Pesanan</h3>

          <div class="summary-rows">
            <div class="summary-row" v-for="item in cartStore.items" :key="item.id">
              <span class="summary-name">{{ item.product.name }} x{{ item.quantity }}</span>
              <span class="summary-val">{{ formatRupiah(item.subtotal) }}</span>
            </div>
          </div>

          <div class="summary-divider"></div>

          <div class="summary-total">
            <span>Total</span>
            <span class="total-value">{{ formatRupiah(cartStore.totalPrice) }}</span>
          </div>

          <div class="summary-note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="14" height="14">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="8" x2="12" y2="12"/>
              <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span>Harga belum termasuk ongkos kirim. Akan dikonfirmasi via WhatsApp.</span>
          </div>

          <!-- Tombol Checkout WA -->
          <button class="btn-checkout" @click="checkout">
            <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            Checkout via WhatsApp
          </button>

          <button class="btn-continue" @click="$inertia.visit('/products')">
            Lanjut Belanja
          </button>
        </div>

      </div>

    </section>
  </MainLayout>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'

export default {
  name: 'CartIndex',
  components: { MainLayout },

  setup() {
    const cartStore = useCartStore()
    const authStore = useAuthStore()
    return { cartStore, authStore }
  },

  data() {
    return {
      loadingItem: null,
      clearing: false,
    }
  },

  mounted() {
    // Redirect ke login jika belum login
    if (!this.authStore.isLoggedIn) {
      this.$inertia.visit('/login')
      return
    }
    this.cartStore.fetchCart()
  },

  methods: {
    async increaseQty(item) {
      this.loadingItem = item.id
      try {
        await this.cartStore.updateQty(item.id, item.quantity + 1)
      } catch (error) {
        alert(error.message)
      } finally {
        this.loadingItem = null
      }
    },

    async decreaseQty(item) {
      if (item.quantity <= 1) return
      this.loadingItem = item.id
      try {
        await this.cartStore.updateQty(item.id, item.quantity - 1)
      } catch (error) {
        alert(error.message)
      } finally {
        this.loadingItem = null
      }
    },

    async removeItem(cartId) {
      if (!confirm('Hapus produk dari keranjang?')) return
      this.loadingItem = cartId
      try {
        await this.cartStore.remove(cartId)
      } catch (error) {
        alert(error.message)
      } finally {
        this.loadingItem = null
      }
    },

    async confirmClear() {
      if (!confirm('Kosongkan semua keranjang?')) return
      this.clearing = true
      try {
        await this.cartStore.clear()
      } catch (error) {
        alert(error.message)
      } finally {
        this.clearing = false
      }
    },

    checkout() {
      if (this.cartStore.items.length === 0) return
      this.cartStore.checkoutViaWA()
    },

    formatRupiah(value) {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
      }).format(value)
    },
  },
}
</script>

<style scoped>
/* ── PAGE ─────────────────────────────────────────── */
.cart-page {
  min-height: 80vh;
  padding: 60px 80px;
  background: #F5F0E8;
  font-family: 'DM Sans', sans-serif;
  color: #1C1208;
}
.cart-header {
  margin-bottom: 40px;
}
.cart-title {
  font-family: 'Playfair Display', serif;
  font-size: 38px;
  color: #1C1208;
  margin-bottom: 6px;
}
.cart-sub {
  font-size: 14px;
  color: rgba(28, 18, 8, 0.5);
}

/* ── LOADING ──────────────────────────────────────── */
.cart-loading {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.skeleton {
  height: 100px;
  background: rgba(28, 18, 8, 0.06);
  border-radius: 8px;
  animation: pulse 1.5s ease-in-out infinite;
}
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.4; }
}

/* ── KOSONG ───────────────────────────────────────── */
.cart-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 20px;
  text-align: center;
  gap: 16px;
}
.empty-icon {
  width: 80px;
  height: 80px;
  background: rgba(28, 18, 8, 0.06);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: rgba(28, 18, 8, 0.3);
}
.empty-icon svg { width: 36px; height: 36px; }
.empty-title {
  font-family: 'Playfair Display', serif;
  font-size: 24px;
  color: #1C1208;
}
.empty-desc {
  font-size: 14px;
  color: rgba(28, 18, 8, 0.5);
}

/* ── LAYOUT CART ──────────────────────────────────── */
.cart-content {
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: 32px;
  align-items: start;
}

/* ── ITEM ─────────────────────────────────────────── */
.cart-items {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.cart-item {
  display: flex;
  align-items: center;
  gap: 16px;
  background: #ffffff;
  border: 0.5px solid rgba(28, 18, 8, 0.08);
  border-radius: 8px;
  padding: 16px;
  transition: border-color 0.2s;
}
.cart-item:hover {
  border-color: rgba(45, 90, 39, 0.2);
}
.item-img {
  width: 80px;
  height: 80px;
  border-radius: 6px;
  overflow: hidden;
  flex-shrink: 0;
  background: #EDE8DF;
  display: flex;
  align-items: center;
  justify-content: center;
}
.item-img img { width: 100%; height: 100%; object-fit: cover; }
.item-img-placeholder { color: rgba(28, 18, 8, 0.2); }
.item-info { flex: 1; }
.item-badge {
  font-size: 10px;
  background: rgba(45, 90, 39, 0.1);
  color: #2D5A27;
  padding: 2px 7px;
  border-radius: 3px;
  display: inline-block;
  margin-bottom: 6px;
}
.item-name {
  font-size: 14px;
  font-weight: 500;
  color: #1C1208;
  margin-bottom: 4px;
}
.item-price {
  font-size: 13px;
  color: rgba(28, 18, 8, 0.5);
}

/* ── QUANTITY ─────────────────────────────────────── */
.item-qty {
  display: flex;
  align-items: center;
  gap: 8px;
  border: 0.5px solid rgba(28, 18, 8, 0.15);
  border-radius: 6px;
  overflow: hidden;
}
.qty-btn {
  width: 32px;
  height: 32px;
  background: transparent;
  border: none;
  cursor: pointer;
  font-size: 16px;
  color: #1C1208;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}
.qty-btn:hover:not(:disabled) { background: rgba(28, 18, 8, 0.05); }
.qty-btn:disabled { opacity: 0.3; cursor: not-allowed; }
.qty-num {
  font-size: 14px;
  font-weight: 500;
  min-width: 28px;
  text-align: center;
  color: #1C1208;
}

/* ── SUBTOTAL ─────────────────────────────────────── */
.item-subtotal { text-align: right; min-width: 100px; }
.subtotal-label {
  font-size: 11px;
  color: rgba(28, 18, 8, 0.4);
  margin-bottom: 2px;
}
.subtotal-value {
  font-size: 14px;
  font-weight: 500;
  color: #2D5A27;
}

/* ── HAPUS ────────────────────────────────────────── */
.item-remove {
  background: none;
  border: none;
  cursor: pointer;
  color: rgba(28, 18, 8, 0.3);
  padding: 6px;
  border-radius: 4px;
  transition: all 0.2s;
  display: flex;
  align-items: center;
}
.item-remove:hover:not(:disabled) {
  color: #8B1A1A;
  background: rgba(139, 26, 26, 0.06);
}
.item-remove:disabled { opacity: 0.3; cursor: not-allowed; }

/* ── CART ACTIONS ─────────────────────────────────── */
.cart-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 8px;
}
.btn-clear {
  font-size: 12px;
  color: rgba(139, 26, 26, 0.7);
  background: none;
  border: 0.5px solid rgba(139, 26, 26, 0.2);
  border-radius: 4px;
  padding: 6px 14px;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.2s;
}
.btn-clear:hover:not(:disabled) {
  background: rgba(139, 26, 26, 0.06);
  border-color: rgba(139, 26, 26, 0.4);
}
.btn-clear:disabled { opacity: 0.5; cursor: not-allowed; }

/* ── SUMMARY ──────────────────────────────────────── */
.cart-summary {
  background: #ffffff;
  border: 0.5px solid rgba(28, 18, 8, 0.08);
  border-radius: 8px;
  padding: 24px;
  position: sticky;
  top: 90px;
}
.summary-title {
  font-family: 'Playfair Display', serif;
  font-size: 20px;
  color: #1C1208;
  margin-bottom: 20px;
}
.summary-rows {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 16px;
}
.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 12px;
}
.summary-name {
  font-size: 13px;
  color: rgba(28, 18, 8, 0.6);
  flex: 1;
  line-height: 1.4;
}
.summary-val {
  font-size: 13px;
  color: #1C1208;
  white-space: nowrap;
}
.summary-divider {
  height: 0.5px;
  background: rgba(28, 18, 8, 0.1);
  margin: 16px 0;
}
.summary-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}
.summary-total span {
  font-size: 15px;
  font-weight: 500;
  color: #1C1208;
}
.total-value {
  font-size: 18px !important;
  color: #2D5A27 !important;
}
.summary-note {
  display: flex;
  align-items: flex-start;
  gap: 6px;
  padding: 10px 12px;
  background: rgba(45, 90, 39, 0.06);
  border-radius: 6px;
  margin-bottom: 20px;
  color: rgba(28, 18, 8, 0.55);
}
.summary-note svg { flex-shrink: 0; margin-top: 1px; }
.summary-note span { font-size: 12px; line-height: 1.5; }

/* ── TOMBOL ───────────────────────────────────────── */
.btn-checkout {
  width: 100%;
  padding: 14px;
  background: #2D5A27;
  color: #F5F0E8;
  border: none;
  border-radius: 6px;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  font-weight: 500;
  letter-spacing: 0.04em;
  cursor: pointer;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  margin-bottom: 10px;
}
.btn-checkout:hover { background: #3a7233; }
.btn-continue {
  width: 100%;
  padding: 12px;
  background: transparent;
  color: rgba(28, 18, 8, 0.6);
  border: 0.5px solid rgba(28, 18, 8, 0.2);
  border-radius: 6px;
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-continue:hover {
  border-color: rgba(28, 18, 8, 0.4);
  color: #1C1208;
}
.btn-primary {
  padding: 12px 28px;
  background: #8B1A1A;
  color: #F5F0E8;
  border: none;
  border-radius: 6px;
  font-family: inherit;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-primary:hover { background: #a01f1f; }

/* ── RESPONSIVE ───────────────────────────────────── */
@media (max-width: 768px) {
  .cart-page { padding: 40px 20px; }
  .cart-content { grid-template-columns: 1fr; }
  .cart-item { flex-wrap: wrap; }
  .item-subtotal { min-width: auto; }
  .cart-summary { position: static; }
}
</style>