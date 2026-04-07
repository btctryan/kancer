<template>
  <div class="auth-page">

    <!-- KIRI: Foto Ranch -->
    <div class="auth-left">
      <img src="/images/about-ranch.jpg" alt="KANCER Ranch" class="auth-bg-img" />
      <div class="auth-left-overlay"></div>
      <div class="auth-left-content">
        <img src="/images/logo.png" alt="KANCER" class="auth-logo" />
        <p class="auth-left-tagline">#percayakancer</p>
      </div>
    </div>

    <!-- KANAN: Form -->
    <div class="auth-right">
      <div class="auth-box">

        <!-- STEP 1: Input Email -->
        <template v-if="step === 1">
          <div class="auth-header">
            <h1 class="auth-title">Lupa Kata Sandi?</h1>
            <p class="auth-sub">Masukkan email kamu, kami akan kirimkan link reset kata sandi.</p>
          </div>

          <div class="alert-error" v-if="errorMsg">{{ errorMsg }}</div>

          <div class="form-group">
            <label>Email</label>
            <input
              type="email"
              v-model="email"
              placeholder="nama@email.com"
              :disabled="loading"
              @keyup.enter="handleSend"
            />
          </div>

          <button class="btn-submit" @click="handleSend" :disabled="loading">
            <span v-if="!loading">Kirim Link Reset</span>
            <span v-else>Mengirim...</span>
          </button>
        </template>

        <!-- STEP 2: Berhasil dikirim -->
        <template v-if="step === 2">
          <div class="success-wrap">
            <div class="success-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="32" height="32">
                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.5 19.79 19.79 0 01.1 .72 2 2 0 012.11 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.08 6.08l1.27-.45a2 2 0 012.11.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
              </svg>
            </div>
            <h2 class="success-title">Email Terkirim!</h2>
            <p class="success-desc">
              Link reset kata sandi sudah dikirim ke<br>
              <strong>{{ email }}</strong>
            </p>
            <p class="success-note">
              Cek inbox atau folder spam kamu. Link berlaku selama 60 menit.
            </p>
            <button class="btn-resend" @click="handleResend" :disabled="resendCooldown > 0">
              <span v-if="resendCooldown > 0">Kirim ulang dalam {{ resendCooldown }}s</span>
              <span v-else>Kirim ulang email</span>
            </button>
          </div>
        </template>

        <!-- Link kembali ke login -->
        <p class="auth-switch">
          <a class="link" @click="$inertia.visit('/login')">← Kembali ke halaman masuk</a>
        </p>

      </div>
    </div>

  </div>
</template>

<script>
import { useAuthStore } from '@/stores/auth'

export default {
  name: 'ForgotPassword',

  setup() {
    const authStore = useAuthStore()
    return { authStore }
  },

  data() {
    return {
      step: 1,
      email: '',
      loading: false,
      errorMsg: '',
      resendCooldown: 0,
      cooldownTimer: null,
    }
  },

  beforeUnmount() {
    if (this.cooldownTimer) clearInterval(this.cooldownTimer)
  },

  methods: {
    async handleSend() {
      this.errorMsg = ''

      if (!this.email.trim()) {
        this.errorMsg = 'Email wajib diisi.'
        return
      }
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) {
        this.errorMsg = 'Format email tidak valid.'
        return
      }

      this.loading = true
      try {
        await this.authStore.forgotPassword(this.email)
        this.step = 2
        this.startCooldown()
      } catch (error) {
        this.errorMsg = error.message || 'Gagal mengirim email. Coba lagi.'
      } finally {
        this.loading = false
      }
    },

    async handleResend() {
      this.loading = true
      try {
        await this.authStore.forgotPassword(this.email)
        this.startCooldown()
      } catch (error) {
        this.errorMsg = error.message || 'Gagal mengirim ulang email.'
      } finally {
        this.loading = false
      }
    },

    startCooldown() {
      this.resendCooldown = 60
      this.cooldownTimer = setInterval(() => {
        this.resendCooldown--
        if (this.resendCooldown <= 0) {
          clearInterval(this.cooldownTimer)
        }
      }, 1000)
    },
  },
}
</script>

<style scoped>
/* ── LAYOUT ───────────────────────────────────────── */
.auth-page {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 100vh;
  background: #F5F0E8;
  font-family: 'DM Sans', sans-serif;
}

/* ── KIRI ─────────────────────────────────────────── */
.auth-left {
  position: relative;
  overflow: hidden;
}
.auth-bg-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.auth-left-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to bottom,
    rgba(28, 18, 8, 0.3) 0%,
    rgba(28, 18, 8, 0.7) 100%
  );
}
.auth-left-content {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 16px;
}
.auth-logo {
  height: 80px;
  width: auto;
  object-fit: contain;
}
.auth-left-tagline {
  font-size: 13px;
  color: rgba(245, 240, 232, 0.7);
  letter-spacing: 0.12em;
}

/* ── KANAN ────────────────────────────────────────── */
.auth-right {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
}
.auth-box {
  width: 100%;
  max-width: 400px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.auth-header { margin-bottom: 4px; }
.auth-title {
  font-family: 'Playfair Display', serif;
  font-size: 32px;
  color: #1C1208;
  margin-bottom: 6px;
}
.auth-sub {
  font-size: 14px;
  color: rgba(28, 18, 8, 0.5);
  line-height: 1.6;
}

/* ── ALERT ────────────────────────────────────────── */
.alert-error {
  background: rgba(139, 26, 26, 0.08);
  border: 0.5px solid rgba(139, 26, 26, 0.3);
  border-radius: 6px;
  padding: 12px 14px;
  font-size: 13px;
  color: #8B1A1A;
}

/* ── FORM ─────────────────────────────────────────── */
.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.form-group label {
  font-size: 12px;
  color: rgba(28, 18, 8, 0.55);
  letter-spacing: 0.04em;
}
.form-group input {
  width: 100%;
  padding: 11px 14px;
  background: #ffffff;
  border: 0.5px solid rgba(28, 18, 8, 0.2);
  border-radius: 6px;
  color: #1C1208;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  outline: none;
  transition: border-color 0.2s;
}
.form-group input:focus { border-color: rgba(45, 90, 39, 0.6); }
.form-group input::placeholder { color: rgba(28, 18, 8, 0.25); }
.form-group input:disabled { opacity: 0.6; cursor: not-allowed; }

/* ── SUCCESS ──────────────────────────────────────── */
.success-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 16px;
  padding: 20px 0;
}
.success-icon {
  width: 72px;
  height: 72px;
  background: rgba(45, 90, 39, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #2D5A27;
}
.success-title {
  font-family: 'Playfair Display', serif;
  font-size: 26px;
  color: #1C1208;
}
.success-desc {
  font-size: 14px;
  color: rgba(28, 18, 8, 0.6);
  line-height: 1.7;
}
.success-desc strong {
  color: #1C1208;
  font-weight: 500;
}
.success-note {
  font-size: 12px;
  color: rgba(28, 18, 8, 0.45);
  line-height: 1.6;
}
.btn-resend {
  font-size: 13px;
  color: #2D5A27;
  background: none;
  border: 0.5px solid rgba(45, 90, 39, 0.4);
  border-radius: 6px;
  padding: 8px 20px;
  cursor: pointer;
  font-family: inherit;
  transition: all 0.2s;
}
.btn-resend:hover:not(:disabled) { background: rgba(45, 90, 39, 0.06); }
.btn-resend:disabled { opacity: 0.5; cursor: not-allowed; }

/* ── TOMBOL ───────────────────────────────────────── */
.btn-submit {
  width: 100%;
  padding: 13px;
  background: #2D5A27;
  color: #F5F0E8;
  border: none;
  border-radius: 6px;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  font-weight: 500;
  letter-spacing: 0.06em;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-submit:hover:not(:disabled) { background: #3a7233; }
.btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }

/* ── LINK ─────────────────────────────────────────── */
.link {
  font-size: 13px;
  color: #2D5A27;
  cursor: pointer;
  text-decoration: none;
  transition: opacity 0.2s;
}
.link:hover { opacity: 0.7; }
.auth-switch {
  text-align: center;
  font-size: 13px;
  color: rgba(28, 18, 8, 0.5);
}

/* ── RESPONSIVE ───────────────────────────────────── */
@media (max-width: 768px) {
  .auth-page { grid-template-columns: 1fr; }
  .auth-left { display: none; }
  .auth-right { padding: 60px 24px; }
}
</style>