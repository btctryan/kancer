<template>
    <MainLayout>

        <!-- ═══════════════════════════════════════════
         HERO SECTION
         FOTO: /public/images/hero-banner.jpg
         → Foto landscape kandang/ranch KANCER
         → Ukuran ideal: 1920x1000px
    ═══════════════════════════════════════════ -->
        <section class="hero">
            <div class="hero-bg">
                <img src="/images/background.png" alt="KANCER Ranch" class="hero-bg-img" />
                <div class="hero-overlay"></div>
            </div>
            <div class="hero-content">
                <p class="hero-tag">KANCER</p>
                <h1 class="hero-title">Ranch. Garden.<br>
                    <em>and Everything in Between.</em>
                </h1>
                <p class="hero-desc">
                    Peternakan, Perkebunan, dan Segala Diantaranya.
                </p>
                <div class="hero-cta">
                    <a href="https://wa.me/6282315000834" target="_blank" class="btn-primary">
                        Pesan via WhatsApp
                    </a>
                    <button class="btn-outline"@click="$inertia.visit('/products')">Katalog Kurban 2026</button>
                       <!-- <a @click="$inertia.visit('/products')">Products</a> -->
                </div>
            </div>
            <div class="hero-scroll">
                <span>scroll</span>
                <div class="scroll-line"></div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════
         ABOUT SECTION
         FOTO: /public/images/about-ranch.jpg
         → Foto suasana ranch / kandang dari luar
         → Ukuran ideal: 800x600px
    ═══════════════════════════════════════════ -->
        <section class="about">
            <div class="about-img-wrap">
                <img src="/images/bg.png" alt="KANCER Ranch" class="about-img" />

            </div>
            <div class="about-content">
                <p class="section-tag">Tentang Kami</p>
                <h2 class="section-title">KANCER Ranch & Garden</h2>
                <p class="about-desc">
                    KANCER berangkat dari RANCH & GARDEN—sebagai fondasi dari apa yang kami bangun.
                </p>
                <p class="about-desc">
                    Seiring berjalan, ruang ini berkembang.
                    Tidak hanya tentang beternak dan bertani, tetapi juga berbagai aktivitas, eksplorasi, dan pengalaman
                    yang lahir dari proses yang sama.
                </p>

                <p class="about-desc">
                    “And everything in between” mewakili hal-hal di antaranya—yang mungkin tidak selalu berhubungan
                    langsung, namun tetap terhubung oleh nilai yang sama.
                </p>
                <p class="about-desc">
                    Lebih dari sekadar tempat atau aktivitas, KANCER adalah ruang untuk bertemu, belajar, dan tumbuh
                    bersama—membangun sesuatu yang perlahan mengakar dan bertahan.
                </p>

                <div class="about-stats">
                    <div class="about-stat" v-for="stat in stats" :key="stat.label">
                        <span class="about-stat-num">{{ stat.value }}</span>
                        <span class="about-stat-label">{{ stat.label }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════
         KEUNGGULAN SECTION
         (tidak butuh foto)
    ═══════════════════════════════════════════ -->
        <section class="features">
            <div class="section-header">
                <p class="section-tag">Kenapa #PercayaKancer?</p>
                <h2 class="section-title">Shopping Advantages</h2>
            </div>
            <div class="features-grid">
                <div class="feature-card" v-for="feature in features" :key="feature.title">
                    <div class="feat-icon" v-html="feature.icon"></div>
                    <h3 class="feat-title">{{ feature.title }}</h3>
                    <p class="feat-desc">{{ feature.desc }}</p>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════
         GALERI SECTION
         FOTO yang dibutuhkan (simpan di /public/images/):
         → gallery-1.jpg : Foto domba close-up
         → gallery-2.jpg : Foto kandang dalam
         → gallery-3.jpg : Foto ranch dari luar
         → gallery-4.jpg : Foto proses perawatan
         → gallery-5.jpg : Foto pengiriman / loading domba
         Ukuran ideal: minimal 800x600px tiap foto
    ═══════════════════════════════════════════ -->
        <section class="gallery">
            <div class="section-header">
                <p class="section-tag">Our Ranch</p>
                <h2 class="section-title">Langsung dari Kandang</h2>
            </div>
            <div class="gallery-grid">
                <div class="gallery-item gallery-large">
                    <img src="/images/1.jpg" alt="Domba KANCER" />
                    <div class="gallery-overlay"><span>Domba Pilihan</span></div>
                </div>
                <div class="gallery-item">
                    <img src="/images/2.png" alt="Kandang KANCER" />
                    <div class="gallery-overlay"><span>Kandang Bersih</span></div>
                </div>
                <div class="gallery-item">
                    <img src="/images/3.jpg" alt="Ranch KANCER" />
                    <div class="gallery-overlay"><span>Ranch & Garden</span></div>
                </div>
                <div class="gallery-item">
                    <img src="/images/4.png" alt="Perawatan Domba" />
                    <div class="gallery-overlay"><span>Perawatan Rutin</span></div>
                </div>
                <div class="gallery-item">
                    <img src="/images/5.png" alt="Pengiriman Domba" />
                    <div class="gallery-overlay"><span>Siap Kirim</span></div>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════
         PRODUK SECTION
         FOTO: /public/images/products/domba-1.jpg dst
         → Foto tiap domba/produk (square 600x600px)
         → Nanti diisi dari API Laravel
    ═══════════════════════════════════════════ -->
        <section class="products" id="products">
            <div class="section-header">
                <p class="section-tag">Our Collection</p>
                <h2 class="section-title">Koleksi Domba</h2>
            </div>

            <div v-if="loadingProducts" class="products-loading">
                <div class="skeleton" v-for="n in 4" :key="n"></div>
            </div>

            <div v-else class="products-grid">
                <div class="prod-card" v-for="product in popularProducts" :key="product.id"
                    @click="$inertia.visit(`/products/${product.id}`)">
                    <div class="prod-img">
                        <img v-if="product.image" :src="product.image" :alt="product.name" />
                        <div v-else class="prod-img-placeholder">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <path
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <circle cx="12" cy="13" r="3" />
                            </svg>
                        </div>
                    </div>
                    <div class="prod-info">
                        <span class="prod-badge">{{ product.badge }}</span>
                        <p class="prod-name">{{ product.name }}</p>
                        <div class="prod-bottom">
                            <p class="prod-price">{{ formatRupiah(product.price) }}</p>
                            <button class="btn-add-cart" @click.stop="addToCart(product)">+ Keranjang</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="products-footer">
                <button class="btn-outline-green" @click="$inertia.visit('/products')">
                    Lihat Semua Domba
                </button>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════
         TESTIMONI SECTION
         (tidak butuh foto, tapi bisa tambah
          foto profil pembeli nanti)
    ═══════════════════════════════════════════ -->
        <section class="testimonials">
            <div class="section-header">
                <p class="section-tag">Kata Mereka</p>
                <h2 class="section-title">Testimoni Pembeli</h2>
            </div>
            <div class="testi-grid">
                <div class="testi-card" v-for="testi in testimonials" :key="testi.name">
                    <div class="testi-stars">★★★★★</div>
                    <p class="testi-text">"{{ testi.text }}"</p>
                    <div class="testi-author">
                        <div class="testi-avatar">{{ testi.name.charAt(0) }}</div>
                        <div>
                            <p class="testi-name">{{ testi.name }}</p>
                            <p class="testi-loc">{{ testi.location }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA WHATSAPP -->
        <section class="cta-section">
            <div class="cta-content">
                <p class="section-tag">Siap Pesan?</p>
                <h2 class="cta-title">Hubungi Kami<br>via WhatsApp</h2>
                <p class="cta-desc">Bebas konsultasi, respon cepat, pengiriman terjamin.</p>
                <a href="https://wa.me/6282315000834" target="_blank" class="btn-wa">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                    </svg>
                    Chat Sekarang
                </a>
            </div>
        </section>

    </MainLayout>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue'
import { useCartStore } from '@/stores/cart'

export default {
    name: 'Home',
    components: { MainLayout },

    setup() {
        const cartStore = useCartStore()
        return { cartStore }
    },

    data() {
        return {
            heroImage: null,
            loadingProducts: false,

            // stats: [
            //     { value: '500+', label: 'Pelanggan Puas' },
            //     { value: '100+', label: 'Domba Tersedia' },
            //     { value: '99%', label: 'Kepuasan Pembeli' },
            //     { value: '1 Hari', label: 'Rata-rata Pengiriman' },
            // ],

            features: [
                {
                    title: 'Gratis Pengiriman',
                    desc: 'Tanpa biaya pengiriman untuk wilayah Jabodetabek dan Bandung Raya—nilai lebih yang kami berikan agar Anda fokus pada kualitas, bukan tambahan biaya.',
                    icon: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                   <rect x="1" y="3" width="15" height="13"/>
                   <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                   <circle cx="5.5" cy="18.5" r="2.5"/>
                   <circle cx="18.5" cy="18.5" r="2.5"/>
                 </svg>`,
                },
                {
                    title: 'Kualitas Terjamin',
                    desc: 'Domba yang kami tawarkan bukan hasil perputaran cepat, melainkan melalui proses seleksi dan perawatan hingga benar-benar siap.',
                    icon: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                   <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                 </svg>`,
                },
                {
                    title: 'Transaksi Aman',
                    desc: 'Kami memastikan setiap transaksi berjalan aman—dan jika terdapat ketidaksesuaian, kami yang akan menanggung penyelesaiannya.',
                    icon: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                   <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                   <path d="M7 11V7a5 5 0 0110 0v4"/>
                 </svg>`,
                },
            ],

            popularProducts: [
                { id: 1, name: 'Domba Garut Jantan', price: 2500000, badge: 'Terlaris', image: null },
                { id: 2, name: 'Domba Merino', price: 3200000, badge: 'Baru', image: null },
                { id: 3, name: 'Domba Ekor Tipis', price: 1800000, badge: 'Diskon', image: null },
                { id: 4, name: 'Domba Batur', price: 4500000, badge: 'Limited', image: null },
            ],

            testimonials: [
                {
                    name: 'Ahmad Fauzi',
                    location: 'Jakarta Selatan',
                    text: 'Dombanya sehat dan sesuai ekspektasi. Pengiriman tepat waktu, pelayanan ramah. Recommended!',
                },
                {
                    name: 'Budi Santoso',
                    location: 'Depok',
                    text: 'Sudah 2 tahun qurban di KANCER. Selalu puas dengan kualitas dan transparansi peternakannya.',
                },
                {
                    name: 'Siti Rahayu',
                    location: 'Bekasi',
                    text: 'Bisa lihat langsung kondisi domba sebelum beli. Prosesnya mudah dan terpercaya banget!',
                },
            ],
        }
    },

    mounted() {
        this.fetchPopularProducts()
    },

    methods: {
        async fetchPopularProducts() {
            this.loadingProducts = true
            try {
                const response = await fetch('/api/products?popular=true&limit=4')
                const data = await response.json()
                if (data && data.length) this.popularProducts = data
            } catch (error) {
                console.error('Gagal memuat produk:', error)
            } finally {
                this.loadingProducts = false
            }
        },

        addToCart(product) {
            this.cartStore.add(product)
            alert(`"${product.name}" ditambahkan ke keranjang!`)
        },

        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
            }).format(value)
        },

        scrollTo(id) {
            document.getElementById(id)?.scrollIntoView({ behavior: 'smooth' })
        },
    },
}
</script>

<style scoped>
/* ── HERO ─────────────────────────────────────────── */
.hero {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.hero-bg {
    position: absolute;
    inset: 0;
    z-index: 0;
}

.hero-bg-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom,
            rgba(28, 18, 8, 0.3) 0%,
            rgba(28, 18, 8, 0.6) 60%,
            rgba(28, 18, 8, 0.8) 100%);
}

.hero-content {
    position: relative;
    z-index: 1;
    text-align: center;
    color: #F5F0E8;
    max-width: 700px;
    padding: 0 20px;
}

.hero-tag {
    font-family: 'Playfair Display', serif;
    font-size: 50px;
    letter-spacing: 0.18em;
    color: #cfc9af;
    text-transform: uppercase;
    margin-bottom: 20px;
}

.hero-title {
    font-family: 'Playfair Display', serif;
    font-size: 64px;
    line-height: 1.08;
    color: #F5F0E8;
    margin-bottom: 24px;
}

.hero-title em {
    font-size: 24px;
    color: #F5F0E8;
    font-style: italic;
}

.hero-desc {
    font-size: 16px;
    color: rgba(245, 240, 232, 0.75);
    line-height: 1.7;
    margin-bottom: 40px;
}

.hero-cta {
    display: flex;
    gap: 12px;
    justify-content: center;
}

.hero-scroll {
    position: absolute;
    bottom: 32px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    z-index: 1;
}

.hero-scroll span {
    font-size: 10px;
    letter-spacing: 0.14em;
    color: rgba(245, 240, 232, 0.5);
    text-transform: uppercase;
}

.scroll-line {
    width: 1px;
    height: 40px;
    background: linear-gradient(to bottom, rgba(245, 240, 232, 0.5), transparent);
    animation: scrollAnim 1.5s ease-in-out infinite;
}

@keyframes scrollAnim {

    0%,
    100% {
        opacity: 1;
        transform: scaleY(1);
    }

    50% {
        opacity: 0.3;
        transform: scaleY(0.5);
    }
}

/* ── ABOUT ────────────────────────────────────────── */
.about {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    padding: 100px 80px;
    background: #F5F0E8;
    align-items: center;
}

.about-img-wrap {
    position: relative;
}

.about-img {
    width: 100%;
    height: 500px;
    object-fit: cover;
    border-radius: 8px;
}

.about-badge {
    position: absolute;
    bottom: -20px;
    right: -20px;
    background: #2D5A27;
    color: #F5F0E8;
    width: 90px;
    height: 90px;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
}

.badge-num {
    font-size: 10px;
    letter-spacing: 0.08em;
    opacity: 0.8;
}

.badge-year {
    font-family: 'Playfair Display', serif;
    font-size: 22px;
    font-weight: 700;
}

.about-content {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.about-desc {
    font-size: 15px;
    color: rgba(28, 18, 8, 0.65);
    line-height: 1.8;
}

.about-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-top: 16px;
    padding-top: 24px;
    border-top: 0.5px solid rgba(28, 18, 8, 0.1);
}

.about-stat {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.about-stat-num {
    font-family: 'Playfair Display', serif;
    font-size: 28px;
    color: #2D5A27;
}

.about-stat-label {
    font-size: 12px;
    color: rgba(28, 18, 8, 0.5);
    letter-spacing: 0.04em;
}

/* ── FEATURES ─────────────────────────────────────── */
.features {
    padding: 80px 80px;
    background: #EDE8DF;
    color: #1C1208;
}

.section-header {
    text-align: center;
    margin-bottom: 48px;
}

.section-tag {
    font-size: 20px;
    letter-spacing: 0.14em;
    color: #2D5A27;
    text-transform: uppercase;
    margin-bottom: 12px;
}

.section-title {
    font-family: 'Playfair Display', serif;
    font-size: 38px;
    color: #1C1208;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.feature-card {
    background: #F5F0E8;
    border: 0.5px solid rgba(28, 18, 8, 0.1);
    border-radius: 8px;
    padding: 28px 24px;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.feature-card:hover {
    border-color: rgba(45, 90, 39, 0.4);
    box-shadow: 0 4px 20px rgba(28, 18, 8, 0.06);
}

.feat-icon {
    width: 44px;
    height: 44px;
    border: 0.5px solid rgba(45, 90, 39, 0.4);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 16px;
    color: #2D5A27;
}

.feat-icon svg {
    width: 20px;
    height: 20px;
}

.feat-title {
    font-size: 15px;
    font-weight: 500;
    color: #1C1208;
    margin-bottom: 8px;
}

.feat-desc {
    font-size: 13px;
    color: rgba(28, 18, 8, 0.55);
    line-height: 1.6;
}

/* ── GALLERY ──────────────────────────────────────── */
.gallery {
    padding: 80px 80px;
    background: #F5F0E8;
}

.gallery-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    grid-template-rows: 240px 240px;
    gap: 12px;
    margin-top: 40px;
}

.gallery-item {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    cursor: pointer;
}

.gallery-large {
    grid-row: 1 / 3;
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.gallery-item:hover img {
    transform: scale(1.05);
}

.gallery-overlay {
    position: absolute;
    inset: 0;
    background: rgba(28, 18, 8, 0);
    display: flex;
    align-items: flex-end;
    padding: 16px;
    transition: background 0.3s;
}

.gallery-item:hover .gallery-overlay {
    background: rgba(28, 18, 8, 0.4);
}

.gallery-overlay span {
    font-size: 13px;
    color: #F5F0E8;
    letter-spacing: 0.06em;
    opacity: 0;
    transition: opacity 0.3s;
}

.gallery-item:hover .gallery-overlay span {
    opacity: 1;
}

/* ── PRODUCTS ─────────────────────────────────────── */
.products {
    padding: 80px 80px;
    background: #EDE8DF;
    color: #1C1208;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-top: 40px;
}

.products-loading {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-top: 40px;
}

.skeleton {
    height: 280px;
    background: rgba(28, 18, 8, 0.06);
    border-radius: 8px;
    animation: pulse 1.5s ease-in-out infinite;
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

.prod-card {
    background: #F5F0E8;
    border: 0.5px solid rgba(28, 18, 8, 0.08);
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.prod-card:hover {
    border-color: rgba(45, 90, 39, 0.3);
    box-shadow: 0 4px 16px rgba(28, 18, 8, 0.08);
}

.prod-img {
    height: 200px;
    background: #ddd5c4;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.prod-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.prod-img svg {
    width: 40px;
    height: 40px;
    color: rgba(28, 18, 8, 0.2);
}

.prod-img-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.prod-info {
    padding: 14px;
}

.prod-badge {
    font-size: 10px;
    background: rgba(45, 90, 39, 0.1);
    color: #2D5A27;
    padding: 2px 7px;
    border-radius: 3px;
    display: inline-block;
    margin-bottom: 8px;
    letter-spacing: 0.04em;
}

.prod-name {
    font-size: 13px;
    font-weight: 500;
    color: #1C1208;
    margin-bottom: 8px;
}

.prod-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.prod-price {
    font-size: 14px;
    color: #2D5A27;
    font-weight: 500;
}

.btn-add-cart {
    font-size: 11px;
    padding: 5px 10px;
    background: transparent;
    border: 0.5px solid rgba(45, 90, 39, 0.4);
    border-radius: 4px;
    color: #2D5A27;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.2s;
    white-space: nowrap;
}

.btn-add-cart:hover {
    background: rgba(45, 90, 39, 0.08);
}

.products-footer {
    text-align: center;
    margin-top: 40px;
}

/* ── TESTIMONI ────────────────────────────────────── */
.testimonials {
    padding: 80px 80px;
    background: #F5F0E8;
}

.testi-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 40px;
}

.testi-card {
    background: #ffffff;
    border: 0.5px solid rgba(28, 18, 8, 0.08);
    border-radius: 8px;
    padding: 28px 24px;
}

.testi-stars {
    color: #c9a96e;
    font-size: 14px;
    margin-bottom: 12px;
    letter-spacing: 2px;
}

.testi-text {
    font-size: 14px;
    color: rgba(28, 18, 8, 0.7);
    line-height: 1.7;
    margin-bottom: 20px;
    font-style: italic;
}

.testi-author {
    display: flex;
    align-items: center;
    gap: 12px;
}

.testi-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #2D5A27;
    color: #F5F0E8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 500;
    flex-shrink: 0;
}

.testi-name {
    font-size: 13px;
    font-weight: 500;
    color: #1C1208;
}

.testi-loc {
    font-size: 12px;
    color: rgba(28, 18, 8, 0.45);
}

/* ── CTA WHATSAPP ─────────────────────────────────── */
.cta-section {
    padding: 100px 80px;
    background: #2D5A27;
    text-align: center;
}

.cta-content {
    max-width: 560px;
    margin: 0 auto;
}

.cta-section .section-tag {
    color: rgba(245, 240, 232, 0.6);
}

.cta-title {
    font-family: 'Playfair Display', serif;
    font-size: 42px;
    color: #F5F0E8;
    line-height: 1.2;
    margin-bottom: 16px;
}

.cta-desc {
    font-size: 15px;
    color: rgba(245, 240, 232, 0.65);
    margin-bottom: 36px;
    line-height: 1.6;
}

.btn-wa {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 16px 36px;
    background: #F5F0E8;
    color: #2D5A27;
    border-radius: 4px;
    font-family: inherit;
    font-size: 14px;
    font-weight: 500;
    letter-spacing: 0.06em;
    text-decoration: none;
    transition: background 0.2s;
}

.btn-wa:hover {
    background: #ffffff;
}

/* ── TOMBOL ───────────────────────────────────────── */
.btn-primary {
    padding: 14px 32px;
    background: #8B1A1A;
    color: #F5F0E8;
    border: none;
    border-radius: 4px;
    font-family: inherit;
    font-size: 13px;
    font-weight: 500;
    letter-spacing: 0.06em;
    cursor: pointer;
    transition: background 0.2s;
    text-decoration: none;
    display: inline-block;
}

.btn-primary:hover {
    background: #a01f1f;
}

.btn-outline {
    padding: 14px 32px;
    background: transparent;
    color: #F5F0E8;
    border: 0.5px solid rgba(245, 240, 232, 0.5);
    border-radius: 4px;
    font-family: inherit;
    font-size: 13px;
    letter-spacing: 0.06em;
    cursor: pointer;
    transition: border-color 0.2s;
}

.btn-outline:hover {
    border-color: #F5F0E8;
}

.btn-outline-green {
    padding: 14px 32px;
    background: transparent;
    color: #2D5A27;
    border: 0.5px solid rgba(45, 90, 39, 0.4);
    border-radius: 4px;
    font-family: inherit;
    font-size: 13px;
    letter-spacing: 0.06em;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-outline-green:hover {
    background: rgba(45, 90, 39, 0.08);
    border-color: rgba(45, 90, 39, 0.7);
}

/* ── RESPONSIVE ───────────────────────────────────── */
@media (max-width: 768px) {
    .hero-title {
        font-size: 38px;
    }

    .about {
        grid-template-columns: 1fr;
        padding: 60px 20px;
        gap: 40px;
    }

    .about-img {
        height: 300px;
    }

    .about-badge {
        bottom: -16px;
        right: 16px;
    }

    .features {
        padding: 60px 20px;
    }

    .features-grid {
        grid-template-columns: 1fr;
    }

    .gallery {
        padding: 60px 20px;
    }

    .gallery-grid {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: auto;
    }

    .gallery-large {
        grid-row: auto;
    }

    .products {
        padding: 60px 20px;
    }

    .products-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .testimonials {
        padding: 60px 20px;
    }

    .testi-grid {
        grid-template-columns: 1fr;
    }

    .cta-section {
        padding: 60px 20px;
    }

    .cta-title {
        font-size: 30px;
    }
}
</style>