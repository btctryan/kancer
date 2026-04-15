<template>
    <MainLayout>
        <section class="product-detail">

            <!-- LOADING -->
            <div v-if="loading" class="detail-loading">
                <div class="skeleton-img"></div>
                <div class="skeleton-info">
                    <div class="skeleton-line w60"></div>
                    <div class="skeleton-line w40"></div>
                    <div class="skeleton-line w80"></div>
                    <div class="skeleton-line w50"></div>
                </div>
            </div>

            <!-- PRODUK -->
            <div v-else-if="product" class="detail-content">

                <!-- KIRI: Foto -->
                <div class="detail-img-wrap">
                    <img v-if="product.image" :src="product.image" :alt="product.name" class="detail-img" />
                    <div v-else class="detail-img-placeholder">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" width="60"
                            height="60">
                            <path
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <circle cx="12" cy="13" r="3" />
                        </svg>
                        <p>Foto segera hadir</p>
                    </div>
                    <span class="detail-badge" v-if="product.badge">{{ product.badge }}</span>
                </div>

                <!-- KANAN: Info -->
                <div class="detail-info">

                    <p class="detail-tag">KANCER Ranch & Garden</p>
                    <h1 class="detail-name">{{ product.name }}</h1>

                    <div class="detail-meta">
                        <div class="meta-item" v-if="product.weight">
                            <span class="meta-label">Berat</span>
                            <span class="meta-val">{{ product.weight }} kg</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Stok</span>
                            <span class="meta-val" :class="product.stock > 0 ? 'stock-in' : 'stock-out'">
                                {{ product.stock > 0 ? product.stock + ' ekor tersedia' : 'Stok habis' }}
                            </span>
                        </div>
                    </div>

                    <div class="detail-price">{{ formatRupiah(product.price) }}</div>

                    <p class="detail-desc" v-if="product.description">{{ product.description }}</p>
                    <p class="detail-desc" v-else>
                        Domba penggemukan dari KANCER Ranch & Garden.<br>
                        Dirawat intensif dengan pakan bergizi.<br>
                        <strong>Harga sudah termasuk biaya pengiriman ke seluruh Jabodebek & Bandung Raya.</strong><br>
                        <strong>Garansi domba sehat sampai tujuan.</strong>
                    </p>





                    <!-- Quantity -->
                    <div class="qty-section" v-if="product.stock > 0">
                        <label class="qty-label">Jumlah</label>
                        <div class="qty-control">
                            <button class="qty-btn" @click="decreaseQty" :disabled="qty <= 1">−</button>
                            <span class="qty-num">{{ qty }}</span>
                            <button class="qty-btn" @click="increaseQty" :disabled="qty >= product.stock">+</button>
                        </div>
                    </div>

                    <!-- Tombol -->
                    <div class="detail-actions">
                        <button class="btn-cart" @click="addToCart" :disabled="product.stock === 0 || addingToCart">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16"
                                height="16">
                                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                                <line x1="3" y1="6" x2="21" y2="6" />
                                <path d="M16 10a4 4 0 01-8 0" />
                            </svg>
                            <span v-if="addingToCart">Menambahkan...</span>
                            <span v-else-if="product.stock === 0">Stok Habis</span>
                            <span v-else>Tambah ke Keranjang</span>
                        </button>

                        <a :href="`https://wa.me/6282315000834?text=${waText}`" target="_blank" class="btn-wa">
                            <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                            Tanya via WhatsApp
                        </a>
                    </div>

                    <!-- Informasi Penting -->
                    <div class="detail-alert">
                        <div class="alert-header">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18"
                                height="18">
                                <path
                                    d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                                <line x1="12" y1="9" x2="12" y2="13" />
                                <line x1="12" y1="17" x2="12.01" y2="17" />
                            </svg>
                            <span class="alert-title">Informasi Penting</span>
                        </div>

                        <div class="alert-content">
                            <div class="alert-item">
                                <span class="alert-bullet"></span>
                                <span>Bobot yang tertera merupakan bobot proyeksi.</span>
                            </div>

                            <div class="alert-item">
                                <span class="alert-bullet"></span>
                                <span>Bobot aktual akan diperbarui pada minggu kedua bulan Mei.</span>
                            </div>

                            <div class="alert-item">
                                <span class="alert-bullet"></span>
                                <span>Untuk domba yang telah terbooking, customer akan menerima video proses penimbangan
                                    sebagai bentuk transparansi.</span>
                            </div>

                            <div class="alert-item">
                                <span class="alert-bullet"></span>
                                <span>Jika domba yang telah terbooking memiliki bobot aktual lebih rendah dari proyeksi,
                                    maka akan dilakukan refund sesuai selisih bobot.</span>
                            </div>

                            <div class="alert-item">
                                <span class="alert-bullet"></span>
                                <span>Jika bobot aktual melebihi proyeksi, maka kelebihan bobot tersebut menjadi bonus
                                    untuk customer.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BACK -->
            <div class="back-wrap">
                <button class="btn-back" @click="$inertia.visit('/products')">
                    ← Kembali ke Produk
                </button>
            </div>

        </section>
    </MainLayout>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'

export default {
    name: 'ProductShow',
    components: { MainLayout },

    props: {
        id: {
            type: [String, Number],
            required: true,
        },
    },

    setup() {
        const cartStore = useCartStore()
        const authStore = useAuthStore()
        return { cartStore, authStore }
    },

    data() {
        return {
            product: null,
            loading: false,
            qty: 1,
            addingToCart: false,
        }
    },

    computed: {
        waText() {
            if (!this.product) return ''
            const pesan = `Halo KANCER! 🐑\n\nSaya tertarik dengan:\n*${this.product.name}*\nHarga: ${this.formatRupiah(this.product.price)}\n\nBoleh minta info lebih lanjut?`
            return encodeURIComponent(pesan)
        },
    },

    mounted() {
        this.fetchProduct()
    },

    methods: {
        async fetchProduct() {
            this.loading = true
            try {
                const response = await fetch(`/api/products/${this.id}`, {
                    headers: { 'Accept': 'application/json' },
                })
                if (response.ok) {
                    this.product = await response.json()
                } else {
                    this.$inertia.visit('/products')
                }
            } catch (error) {
                console.error('Gagal memuat produk:', error)
            } finally {
                this.loading = false
            }
        },

        increaseQty() {
            if (this.qty < this.product.stock) this.qty++
        },

        decreaseQty() {
            if (this.qty > 1) this.qty--
        },

        async addToCart() {
            if (!this.authStore.isLoggedIn) {
                this.$inertia.visit('/login')
                return
            }

            this.addingToCart = true
            try {
                await this.cartStore.add(this.product, this.qty)
                alert(`"${this.product.name}" (${this.qty} ekor) ditambahkan ke keranjang!`)
            } catch (error) {
                alert(error.message || 'Gagal menambahkan ke keranjang.')
            } finally {
                this.addingToCart = false
            }
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
.product-detail {
    min-height: 80vh;
    padding: 60px 80px;
    background: #F5F0E8;
    font-family: 'DM Sans', sans-serif;
    color: #1C1208;
}

/* ── LOADING ──────────────────────────────────────── */
.detail-loading {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
}

.skeleton-img {
    height: 500px;
    background: rgba(28, 18, 8, 0.06);
    border-radius: 8px;
    animation: pulse 1.5s ease-in-out infinite;
}

.skeleton-info {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding-top: 20px;
}

.skeleton-line {
    height: 16px;
    background: rgba(28, 18, 8, 0.06);
    border-radius: 4px;
    animation: pulse 1.5s ease-in-out infinite;
}

.w40 {
    width: 40%;
}

.w60 {
    width: 60%;
}

.w80 {
    width: 80%;
}

.w50 {
    width: 50%;
}

@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.4;
    }
}

/* ── DETAIL ───────────────────────────────────────── */
.detail-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: start;
}

/* ── FOTO ─────────────────────────────────────────── */
.detail-img-wrap {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
}

.detail-img {
    width: 100%;
    height: 500px;
    object-fit: cover;
    border-radius: 8px;
}

.detail-img-placeholder {
    width: 100%;
    height: 500px;
    background: #EDE8DF;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    color: rgba(28, 18, 8, 0.3);
    border-radius: 8px;
}

.detail-img-placeholder p {
    font-size: 13px;
}

.detail-badge {
    position: absolute;
    top: 16px;
    left: 16px;
    font-size: 11px;
    background: #2D5A27;
    color: #F5F0E8;
    padding: 4px 10px;
    border-radius: 4px;
    letter-spacing: 0.04em;
}

/* ── INFO ─────────────────────────────────────────── */
.detail-info {
    display: flex;
    flex-direction: column;
    gap: 20px;
    position: sticky;
    top: 90px;
}

.detail-tag {
    font-size: 11px;
    letter-spacing: 0.14em;
    color: #2D5A27;
    text-transform: uppercase;
}

.detail-name {
    font-family: 'Playfair Display', serif;
    font-size: 36px;
    color: #1C1208;
    line-height: 1.2;
}

.detail-meta {
    display: flex;
    gap: 24px;
}

.meta-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.meta-label {
    font-size: 11px;
    color: rgba(28, 18, 8, 0.45);
    letter-spacing: 0.06em;
    text-transform: uppercase;
}

.meta-val {
    font-size: 14px;
    font-weight: 500;
    color: #1C1208;
}

.stock-in {
    color: #2D5A27;
}

.stock-out {
    color: #8B1A1A;
}

.detail-price {
    font-family: 'Playfair Display', serif;
    font-size: 32px;
    color: #2D5A27;
}

.detail-desc {
    font-size: 14px;
    color: rgba(28, 18, 8, 0.65);
    line-height: 1.8;
}

/* ── QUANTITY ─────────────────────────────────────── */
.qty-section {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.qty-label {
    font-size: 12px;
    color: rgba(28, 18, 8, 0.55);
    letter-spacing: 0.04em;
}

.qty-control {
    display: inline-flex;
    align-items: center;
    width: fit-content;
    max-width: 130px;
    border: 0.5px solid rgba(28, 18, 8, 0.2);
    border-radius: 6px;
    overflow: hidden;
}

.qty-btn {
    width: 40px;
    height: 40px;
    background: transparent;
    border: none;
    cursor: pointer;
    font-size: 18px;
    color: #1C1208;
    transition: background 0.2s;
}

.qty-btn:hover:not(:disabled) {
    background: rgba(28, 18, 8, 0.05);
}

.qty-btn:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.qty-num {
    font-size: 15px;
    font-weight: 500;
    min-width: 48px;
    text-align: center;
    color: #1C1208;
}

/* ── TOMBOL ───────────────────────────────────────── */
.detail-actions {
    display: flex;
    gap: 12px;
}

.btn-cart {
    flex: 1;
    padding: 14px 20px;
    background: #2D5A27;
    color: #F5F0E8;
    border: none;
    border-radius: 6px;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background 0.2s;
}

.btn-cart:hover:not(:disabled) {
    background: #3a7233;
}

.btn-cart:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-wa {
    padding: 14px 20px;
    background: transparent;
    color: #2D5A27;
    border: 0.5px solid rgba(45, 90, 39, 0.4);
    border-radius: 6px;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.2s;
    white-space: nowrap;
}

.btn-wa:hover {
    background: rgba(45, 90, 39, 0.06);
}

/* ── ALERT ─────────────────────────────────────── */
.detail-alert {
    display: flex;
    flex-direction: column;
    gap: 0;
    background: #FFFBF5;
    border-radius: 8px;
    border: 1px solid rgba(184, 134, 11, 0.2);
    overflow: hidden;
}

.alert-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 16px;
    background: rgba(184, 134, 11, 0.08);
    border-bottom: 1px solid rgba(184, 134, 11, 0.15);
}

.alert-header svg {
    color: #B8860B;
    flex-shrink: 0;
}

.alert-title {
    font-size: 14px;
    font-weight: 600;
    color: #1C1208;
    letter-spacing: 0.02em;
}

.alert-content {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 16px;
}

.alert-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 13px;
    color: rgba(28, 18, 8, 0.75);
    line-height: 1.6;
}

.alert-bullet {
    width: 5px;
    height: 5px;
    background: #B8860B;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 7px;
}

.alert-footer {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px 16px;
    background: rgba(45, 90, 39, 0.06);
    border-top: 1px solid rgba(45, 90, 39, 0.15);
    font-size: 13px;
    color: #2D5A27;
    font-weight: 500;
}

/* ── BACK ─────────────────────────────────────────── */
.back-wrap {
    margin-top: 48px;
}

.btn-back {
    font-size: 13px;
    color: rgba(28, 18, 8, 0.5);
    background: none;
    border: none;
    cursor: pointer;
    font-family: inherit;
    transition: color 0.2s;
    padding: 0;
}

.btn-back:hover {
    color: #1C1208;
}

/* ── RESPONSIVE ───────────────────────────────────── */
@media (max-width: 768px) {
    .product-detail {
        padding: 40px 20px;
    }

    .detail-content {
        grid-template-columns: 1fr;
        gap: 32px;
    }

    .detail-loading {
        grid-template-columns: 1fr;
    }

    .detail-img {
        height: 300px;
    }

    .detail-img-placeholder {
        height: 300px;
    }

    .detail-info {
        position: static;
    }

    .detail-name {
        font-size: 28px;
    }

    .detail-actions {
        flex-direction: column;
    }
}
</style>