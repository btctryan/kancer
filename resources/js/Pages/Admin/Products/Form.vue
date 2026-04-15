<!-- src/pages/Admin/Products/Form.vue -->
<template>
  <div class="min-h-screen bg-gray-50">
    <nav class="bg-green-700 text-white px-6 py-4 flex justify-between items-center">
      <h1 class="text-xl font-bold">🛒 Admin Panel</h1>
      <router-link to="/admin/products" class="text-sm underline">← Kembali</router-link>
    </nav>

    <div class="max-w-2xl mx-auto p-6">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">
        {{ isEdit ? 'Edit Produk' : 'Tambah Produk' }}
      </h2>

      <div v-if="error" class="bg-red-100 text-red-600 p-3 rounded-lg mb-4 text-sm">{{ error }}</div>
      <div v-if="success" class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 text-sm">{{ success }}</div>

      <form @submit.prevent="handleSubmit" class="bg-white rounded-2xl shadow p-6 space-y-4">

        <!-- Nama -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk *</label>
          <input v-model="form.name" type="text" required class="input" placeholder="Contoh: Madu Hutan Asli" />
        </div>

        <!-- Harga & Stok -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) *</label>
            <input v-model="form.price" type="number" required min="0" class="input" placeholder="50000" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Stok *</label>
            <input v-model="form.stock" type="number" required min="0" class="input" placeholder="100" />
          </div>
        </div>

        <!-- Berat & Badge -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Berat (gram) *</label>
            <input v-model="form.weight" type="number" required min="0" class="input" placeholder="500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Badge</label>
            <input v-model="form.badge" type="text" class="input" placeholder="Terlaris / Baru" />
          </div>
        </div>

        <!-- Status -->
        <div class="flex items-center gap-3">
          <input v-model="form.is_active" type="checkbox" id="is_active" class="w-4 h-4" />
          <label for="is_active" class="text-sm text-gray-700">Produk Aktif</label>
        </div>

        <!-- Upload Foto -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Foto Produk</label>
          <input type="file" accept="image/*" @change="onFileChange" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-50 file:text-green-700 hover:file:bg-green-100" />
          <!-- Preview -->
          <div class="mt-3" v-if="preview || currentImage">
            <p class="text-xs text-gray-400 mb-1">Preview:</p>
            <img :src="preview || `/storage/${currentImage}`" class="w-32 h-32 object-cover rounded-xl border" />
          </div>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-green-700 transition disabled:opacity-50"
        >
          {{ loading ? 'Menyimpan...' : (isEdit ? 'Update Produk' : 'Simpan Produk') }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAdminStore } from '@/stores/admin'

const route      = useRoute()
const router     = useRouter()
const adminStore = useAdminStore()

const isEdit      = computed(() => !!route.params.id)
const loading     = ref(false)
const error       = ref('')
const success     = ref('')
const preview     = ref(null)
const currentImage = ref(null)
const imageFile   = ref(null)

const form = ref({
  name: '', price: '', stock: '', weight: '', badge: '', is_active: true,
})

onMounted(async () => {
  adminStore.loadToken()
  if (isEdit.value) {
    const product      = await adminStore.getProduct(route.params.id)
    form.value         = { ...product }
    form.value.is_active = !!product.is_active
    currentImage.value = product.image
  }
})

function onFileChange(e) {
  const file = e.target.files[0]
  if (!file) return
  imageFile.value = file
  preview.value   = URL.createObjectURL(file)
}

async function handleSubmit() {
  loading.value = true
  error.value   = ''
  success.value = ''

  try {
    const fd = new FormData()
    fd.append('name',      form.value.name)
    fd.append('price',     form.value.price)
    fd.append('stock',     form.value.stock)
    fd.append('weight',    form.value.weight)
    fd.append('badge',     form.value.badge || '')
    fd.append('is_active', form.value.is_active ? 1 : 0)
    if (imageFile.value) fd.append('image', imageFile.value)

    if (isEdit.value) {
      await adminStore.updateProduct(route.params.id, fd)
      success.value = 'Produk berhasil diupdate!'
    } else {
      await adminStore.createProduct(fd)
      success.value = 'Produk berhasil ditambahkan!'
      setTimeout(() => router.push('/admin/products'), 1000)
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Terjadi kesalahan.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.input {
  @apply w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500;
}
</style>