<template>
    <div class="app-layout">
        <!-- NAVBAR -->
        <nav class="navbar">
            <div class="logo" @click="$inertia.visit('/')">
                <img src="/images/logo.png" alt="Logo" />
            </div>
            <div class="nav-center">
                <div class="nav-links">
                    <a @click="$inertia.visit('/')">Home</a>
                    <a @click="$inertia.visit('/products')">Products</a>
                </div>
            </div>
            <div class="nav-right"></div>
        </nav>

        <!-- KONTEN HALAMAN -->
        <main>
            <slot />
        </main>

        <!-- FOOTER -->
        <footer class="footer">
            <div class="footer-grid">
                <div class="footer-brand">
                    <span class="logo">KANCER</span>
                    <p>Platform belanja domba online terpercaya dengan produk berkualitas dan pengiriman ke Jabodetabek.</p>
                    <p class="footer-tagline">#percayakancer</p>
                </div>
                <div class="footer-col">
                    <h4>Location</h4>
                    <a href="https://maps.app.goo.gl/oJpzMcFtw6T5wSok7" target="_blank" class="footer-location">
                        <span class="location-name">KANCER - RANCH & GARDEN</span>
                        <span class="location-addr">Jl. Ps. Senen No.6, Cimanggu, Kec. Ngamprah, Kabupaten Bandung Barat, Jawa Barat 40552</span>
                    </a>
                </div>
                <div class="footer-col">
                    <h4>About Us</h4>
                    <a href="https://www.instagram.com/thekancers" target="_blank">Instagram</a>
                    <a href="https://wa.me/6282315000834" target="_blank">WhatsApp</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2026 KANCER. #PERCAYAKANCER</p>
            </div>
        </footer>
    </div>
</template>

<script>
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'

export default {
    name: 'MainLayout',
    setup() {
        const cartStore = useCartStore()
        const authStore = useAuthStore()
        return { cartStore, authStore }
    },
}
</script>

<style scoped>
.app-layout {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: #F5F0E8;
    color: #1C1208;
    font-family: 'DM Sans', sans-serif;
}

main {
    flex: 1;
}

/* ── NAVBAR ───────────────────────────────────────── */
.navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 40px;
    height: 60px; /* ← Navbar diperbesar biar logo muat */
    border-bottom: 0.5px solid rgba(28, 18, 8, 0.12);
    position: sticky;
    top: 0;
    background: #F5F0E8;
    z-index: 100;
}

.logo {
    cursor: pointer;
    display: flex;
    align-items: center;
}

.logo img {
    height: 70px; /* ← Logo 70px, navbar 90px = ada space atas bawah */
    width: auto;
    object-fit: contain;
}

.nav-center {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
}

.nav-links {
    display: flex;
    gap: 32px;
    align-items: center;
}

.nav-links a {
    font-size: 13px; /* ← Kembali ke size awal */
    font-weight: 400; /* ← Normal weight (bukan bold) */
    color: rgba(28, 18, 8, 0.6); /* ← Warna agak transparansi seperti awal */
    cursor: pointer;
    letter-spacing: 0.04em;
    transition: color 0.2s;
}

.nav-links a:hover {
    color: #1C1208; /* ← Hover jadi hitam */
}

.nav-right {
    width: 0;
}

/* ── FOOTER ───────────────────────────────────────── */
.footer {
    padding: 48px 40px 28px;
    border-top: 0.5px solid rgba(28, 18, 8, 0.1);
    background: #F5F0E8;
}

.footer-grid {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1fr;
    gap: 40px;
    margin-bottom: 40px;
}

.footer-brand {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.footer-brand p {
    font-size: 13px;
    color: rgba(28, 18, 8, 0.45);
    line-height: 1.7;
    max-width: 240px;
}

.footer-tagline {
    font-size: 12px !important;
    color: #2D5A27 !important;
    letter-spacing: 0.06em;
}

.footer-col h4 {
    font-size: 11px;
    letter-spacing: 0.1em;
    color: rgba(28, 18, 8, 0.5);
    text-transform: uppercase;
    margin-bottom: 14px;
}

.footer-col a {
    display: block;
    font-size: 13px;
    color: rgba(28, 18, 8, 0.5);
    margin-bottom: 8px;
    cursor: pointer;
    transition: color 0.2s;
    text-decoration: none;
}

.footer-col a:hover {
    color: #1C1208;
}

.footer-location {
    display: flex !important;
    flex-direction: column;
    gap: 6px;
}

.location-name {
    font-size: 12px;
    font-weight: 500;
    color: rgba(28, 18, 8, 0.7);
}

.location-addr {
    font-size: 12px;
    color: rgba(28, 18, 8, 0.4);
    line-height: 1.6;
}

.footer-bottom {
    display: flex;
    justify-content: center;
    align-items: center;
    padding-top: 20px;
    border-top: 0.5px solid rgba(28, 18, 8, 0.08);
}

.footer-bottom p {
    font-size: 12px;
    color: rgba(28, 18, 8, 0.3);
    letter-spacing: 0.06em;
}

/* ── RESPONSIVE ───────────────────────────────────── */
@media (max-width: 768px) {
    .navbar {
        padding: 0 20px;
        height: 70px;
    }

    .logo img {
        height: 50px;
    }

    .nav-center {
        position: static;
        transform: none;
        margin-left: auto;
    }

    .nav-right {
        display: none;
    }

    .footer {
        padding: 40px 20px 24px;
    }

    .footer-grid {
        grid-template-columns: 1fr;
        gap: 32px;
    }

    .footer-bottom {
        text-align: center;
    }
}
</style>