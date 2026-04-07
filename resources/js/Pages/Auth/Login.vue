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

    <!-- KANAN: Form Login -->
    <div class="auth-right">
      <div class="auth-box">

        <div class="auth-header">
          <h1 class="auth-title">Selamat Datang</h1>
          <p class="auth-sub">Masuk ke akun KANCER kamu</p>
        </div>

        <!-- Error message -->
        <div class="alert-error" v-if="errorMsg">
          {{ errorMsg }}
        </div>

        <div class="form-group">
          <label>Email</label>
          <input
            type="email"
            v-model="form.email"
            placeholder="nama@email.com"
            :disabled="loading"
            @keyup.enter="handleLogin"
          />
        </div>

        <div class="form-group">
          <label>Kata Sandi</label>
          <div class="input-wrap">
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="form.password"
              placeholder="••••••••"
              :disabled="loading"
              @keyup.enter="handleLogin"
            />
            <button class="toggle-pw" @click="showPassword = !showPassword" type="button">
              <svg v-if="!showPassword" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="16" height="16">
                <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="form-footer">
          <a class="link" @click="$inertia.visit('/forgot-password')">Lupa kata sandi?</a>
        </div>

        <button class="btn-submit" @click="handleLogin" :disabled="loading">
          <span v-if="!loading">Masuk</span>
          <span v-else class="loading-dots">Memuat...</span>
        </button>

        <p class="auth-switch">
          Belum punya akun?
          <a class="link" @click="$inertia.visit('/register')">Daftar sekarang</a>
        </p>

      </div>
    </div>

  </div>
</template>

<script>
import { useAuthStore } from '@/stores/auth'

export default {
  name: 'Login',

  setup() {
    const authStore = useAuthStore()
    return { authStore }
  },

  data() {
    return {
      form: {
        email: '',
        password: '',
      },
      showPassword: false,
      loading: false,
      errorMsg: '',
    }
  },

  methods: {
    async handleLogin() {
      this.errorMsg = ''

      // Validasi sederhana
      if (!this.form.email || !this.form.password) {
        this.errorMsg = 'Email dan kata sandi wajib diisi.'
        return
      }

      this.loading = true
      try {
        await this.authStore.login(this.form.email, this.form.password)
        // Jika berhasil, authStore.login akan redirect ke '/'
      } catch (error) {
        this.errorMsg = error.message || 'Email atau kata sandi salah.'
      } finally {
        this.loading = false
      }
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
.auth-header {
  margin-bottom: 8px;
}
.auth-title {
  font-family: 'Playfair Display', serif;
  font-size: 32px;
  color: #1C1208;
  margin-bottom: 6px;
}
.auth-sub {
  font-size: 14px;
  color: rgba(28, 18, 8, 0.5);
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
.form-group input:focus {
  border-color: rgba(45, 90, 39, 0.6);
}
.form-group input::placeholder {
  color: rgba(28, 18, 8, 0.25);
}
.form-group input:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.input-wrap {
  position: relative;
}
.input-wrap input {
  width: 100%;
  padding-right: 44px;
}
.toggle-pw {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: rgba(28, 18, 8, 0.4);
  display: flex;
  align-items: center;
  padding: 0;
  transition: color 0.2s;
}
.toggle-pw:hover {
  color: rgba(28, 18, 8, 0.7);
}
.form-footer {
  display: flex;
  justify-content: flex-end;
  margin-top: -8px;
}

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
  margin-top: 4px;
}
.btn-submit:hover:not(:disabled) {
  background: #3a7233;
}
.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* ── LINK ─────────────────────────────────────────── */
.link {
  font-size: 13px;
  color: #2D5A27;
  cursor: pointer;
  text-decoration: none;
  transition: opacity 0.2s;
}
.link:hover {
  opacity: 0.7;
}
.auth-switch {
  text-align: center;
  font-size: 13px;
  color: rgba(28, 18, 8, 0.5);
}

/* ── RESPONSIVE ───────────────────────────────────── */
@media (max-width: 768px) {
  .auth-page {
    grid-template-columns: 1fr;
  }
  .auth-left {
    display: none;
  }
  .auth-right {
    padding: 60px 24px;
  }
}
</style>