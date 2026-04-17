<template>
  <MainLayout>
    <section class="products-page">

      <!-- HEADER -->
      <div class="page-header">
        <p class="section-tag">Our Collection</p>
        <h1 class="page-title">Koleksi Domba</h1>
        <p class="page-desc">Pilih domba berkualitas langsung dari ranch kami</p>
      </div>

      <!-- SEARCH & FILTER -->
      <div class="toolbar">
        <div class="search-wrap">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input
            type="text"
            v-model="search"
            placeholder="Cari domba..."
            @input="handleSearch"
          />
        </div>
        <div class="filter-wrap">
          <select v-model="sortBy" @change="handleSort">
            <option value="price_asc">Harga Terendah</option>
            <option value="price_desc">Harga Tertinggi</option>
            <option value="name">Nama A-Z</option>
          </select>
        </div>
      </div>

      <!-- LOADING -->
      <div v-if="loading" class="products-grid">
        <div class="skeleton" v-for="n in 8" :key="n"></div>
      </div>

      <!-- KOSONG -->
      <div v-else-if="filteredProducts.length === 0" class="empty-state">
        <p class="empty-title">Produk tidak ditemukan</p>
        <p class="empty-desc">Coba kata kunci lain</p>
      </div>

      <!-- GRID PRODUK -->
      <div v-else class="products-grid">
        <div
          class="prod-card"
          v-for="product in filteredProducts"
          :key="product.id"
          @click="$inertia.visit(`/products/${product.id}`)"
        >
          <div class="prod-img">
            <img v-if="product.image" :src="product.image" :alt="product.name" />
            <div v-else class="prod-img-placeholder">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" width="36" height="36">
                <path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                <circle cx="12" cy="13" r="3"/>
              </svg>
            </div>
            <span class="prod-badge" v-if="product.badge">{{ product.badge }}</span>
            <span class="prod-stock-out" v-if="product.stock === 0">Habis</span>
          </div>
          <div class="prod-info">
            <p class="prod-name">{{ product.name }}</p>
            <p class="prod-weight" v-if="product.weight">{{ product.weight }} kg</p>
            <div class="prod-bottom">
              <p class="prod-price">{{ formatRupiah(product.price) }}</p>
              <button
                class="btn-add-cart"
                @click.stop="addToCart(product)"
                :disabled="product.stock === 0 || addingToCart === product.id"
              >
                <span v-if="addingToCart === product.id">...</span>
                <span v-else>+ Keranjang</span>
              </button>
            </div>
          </div>
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
  name: 'ProductsIndex',
  components: { MainLayout },

  setup() {
    const cartStore = useCartStore()
    const authStore = useAuthStore()
    return { cartStore, authStore }
  },

  data() {
    return {
      products: [],
      loading: false,
      search: '',
      sortBy: 'price_asc',
      addingToCart: null,
    }
  },

  computed: {
    filteredProducts() {
      let result = [...this.products]

      // Filter search
      if (this.search) {
        result = result.filter(p =>
          p.name.toLowerCase().includes(this.search.toLowerCase())
        )
      }

      // Sort
      if (this.sortBy === 'price_asc') {
        result.sort((a, b) => a.price - b.price)
      } else if (this.sortBy === 'price_desc') {
        result.sort((a, b) => b.price - a.price)
      } else if (this.sortBy === 'name') {
        result.sort((a, b) => a.name.localeCompare(b.name))
      }

      return result
    },
  },

  mounted() {
    this.fetchProducts()
  },

  methods: {
    async fetchProducts() {
      this.loading = true
      try {
        const response = await fetch('/api/products', {
          headers: { 'Accept': 'application/json' },
        })
        const data = await response.json()
        this.products = data
      } catch (error) {
        console.error('Gagal memuat produk:', error)
      } finally {
        this.loading = false
      }
    },

    async addToCart(product) {
      if (!this.authStore.isLoggedIn) {
        this.$inertia.visit('/login')
        return
      }

      this.addingToCart = product.id
      try {
        await this.cartStore.add(product)
        alert(`"${product.name}" ditambahkan ke keranjang!`)
      } catch (error) {
        alert(error.message || 'Gagal menambahkan ke keranjang.')
      } finally {
        this.addingToCart = null
      }
    },

    handleSearch() {
      // search sudah handle lewat computed filteredProducts
    },

    handleSort() {
      // sort sudah handle lewat computed filteredProducts
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
.products-page {
  min-height: 80vh;
  padding: 60px 80px;
  background: #F5F0E8;
  font-family: 'DM Sans', sans-serif;
  color: #1C1208;
}

/* ── HEADER ───────────────────────────────────────── */
.page-header {
  text-align: center;
  margin-bottom: 48px;
}
.section-tag {
  font-size: 11px;
  letter-spacing: 0.14em;
  color: #2D5A27;
  text-transform: uppercase;
  margin-bottom: 12px;
}
.page-title {
  font-family: 'Playfair Display', serif;
  font-size: 42px;
  color: #1C1208;
  margin-bottom: 12px;
}
.page-desc {
  font-size: 15px;
  color: rgba(28, 18, 8, 0.55);
}

/* ── TOOLBAR ──────────────────────────────────────── */
.toolbar {
  display: flex;
  gap: 12px;
  margin-bottom: 32px;
  align-items: center;
}
.search-wrap {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 10px;
  background: #ffffff;
  border: 0.5px solid rgba(28, 18, 8, 0.2);
  border-radius: 6px;
  padding: 10px 14px;
  color: rgba(28, 18, 8, 0.4);
}
.search-wrap input {
  flex: 1;
  border: none;
  outline: none;
  background: transparent;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  color: #1C1208;
}
.search-wrap input::placeholder { color: rgba(28, 18, 8, 0.3); }
.filter-wrap select {
  padding: 10px 14px;
  background: #ffffff;
  border: 0.5px solid rgba(28, 18, 8, 0.2);
  border-radius: 6px;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  color: #1C1208;
  cursor: pointer;
  outline: none;
}

/* ── GRID ─────────────────────────────────────────── */
.products-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}

/* ── SKELETON ─────────────────────────────────────── */
.skeleton {
  height: 320px;
  background: rgba(28, 18, 8, 0.06);
  border-radius: 8px;
  animation: pulse 1.5s ease-in-out infinite;
}
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.4; }
}

/* ── CARD ─────────────────────────────────────────── */
.prod-card {
  background: #ffffff;
  border: 0.5px solid rgba(28, 18, 8, 0.08);
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.prod-card:hover {
  border-color: rgba(45, 90, 39, 0.3);
  box-shadow: 0 4px 20px rgba(28, 18, 8, 0.08);
}
.prod-img {
  height: 220px;
  background: #EDE8DF;
  overflow: hidden;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}
.prod-img img { width: 100%; height: 100%; object-fit: cover; }
.prod-img-placeholder { color: rgba(28, 18, 8, 0.2); }
.prod-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  font-size: 10px;
  background: #2D5A27;
  color: #F5F0E8;
  padding: 3px 8px;
  border-radius: 3px;
  letter-spacing: 0.04em;
}
.prod-stock-out {
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 10px;
  background: #8B1A1A;
  color: #F5F0E8;
  padding: 3px 8px;
  border-radius: 3px;
}
.prod-info { padding: 16px; }
.prod-name {
  font-size: 14px;
  font-weight: 500;
  color: #1C1208;
  margin-bottom: 4px;
}
.prod-weight {
  font-size: 12px;
  color: rgba(28, 18, 8, 0.45);
  margin-bottom: 12px;
}
.prod-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}
.prod-price {
  font-size: 15px;
  color: #2D5A27;
  font-weight: 500;
}
.btn-add-cart {
  font-size: 11px;
  padding: 6px 12px;
  background: transparent;
  border: 0.5px solid rgba(45, 90, 39, 0.4);
  border-radius: 4px;
  color: #2D5A27;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.2s;
  white-space: nowrap;
}
.btn-add-cart:hover:not(:disabled) { background: rgba(45, 90, 39, 0.08); }
.btn-add-cart:disabled { opacity: 0.4; cursor: not-allowed; }

/* ── EMPTY ────────────────────────────────────────── */
.empty-state {
  text-align: center;
  padding: 80px 20px;
}
.empty-title {
  font-family: 'Playfair Display', serif;
  font-size: 24px;
  color: #1C1208;
  margin-bottom: 8px;
}
.empty-desc {
  font-size: 14px;
  color: rgba(28, 18, 8, 0.5);
}

/* ── RESPONSIVE ───────────────────────────────────── */
@media (max-width: 1024px) {
  .products-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
  .products-page { padding: 40px 20px; }
  .products-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
  .toolbar { flex-direction: column; }
  .search-wrap { width: 100%; }
}
</style>