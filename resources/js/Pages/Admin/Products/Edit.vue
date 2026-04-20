<template>
  <AdminLayout :title="`Edit Produk: ${product.code}`">
    <div class="max-w-2xl">
      <div class="bg-white rounded-xl shadow-sm p-8">

        <!-- Preview nama & kategori otomatis -->
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
          <p class="text-xs text-green-600 font-medium mb-1">Preview nama otomatis:</p>
          <p class="text-green-800 font-semibold text-lg">{{ previewName }}</p>
          <span :class="categoryColor(previewCategory)"
            class="mt-1 inline-block px-2 py-0.5 rounded-full text-xs font-medium">
            {{ previewCategory }}
          </span>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
          <div class="grid grid-cols-2 gap-5">

            <!-- Kode Produk -->
            <div class="col-span-2">
              <label class="label">Kode Produk *</label>
              <input
                v-model="form.code"
                type="text"
                placeholder="KNCR001"
                class="input font-mono"
                :class="{ 'border-red-400': form.errors.code }"
              />
              <p v-if="form.errors.code" class="error">{{ form.errors.code }}</p>
            </div>

            <!-- Harga -->
            <div>
              <label class="label">Harga (Rp) *</label>
              <input
                v-model="form.price"
                type="number"
                placeholder="3500000"
                class="input"
                :class="{ 'border-red-400': form.errors.price }"
              />
              <p v-if="form.errors.price" class="error">{{ form.errors.price }}</p>
              <p class="text-xs text-gray-400 mt-1">
                &lt; 3.600.000 = Ekonomis | 3.720.000–4.080.000 = Medium | &gt; 4.200.000 = Premium
              </p>
            </div>

            <!-- Berat -->
            <div>
              <label class="label">Berat (kg) *</label>
              <input
                v-model="form.weight"
                type="number"
                step="0.1"
                placeholder="25.5"
                class="input"
                :class="{ 'border-red-400': form.errors.weight }"
              />
              <p v-if="form.errors.weight" class="error">{{ form.errors.weight }}</p>
            </div>

            <!-- Stok -->
            <div>
              <label class="label">Stok (ekor) *</label>
              <input
                v-model="form.stock"
                type="number"
                placeholder="10"
                class="input"
                :class="{ 'border-red-400': form.errors.stock }"
              />
              <p v-if="form.errors.stock" class="error">{{ form.errors.stock }}</p>
            </div>

            <!-- Status Aktif -->
            <div class="flex items-center gap-3 pt-6">
              <input
                v-model="form.is_active"
                type="checkbox"
                id="is_active"
                class="w-4 h-4 accent-green-600"
              />
              <label for="is_active" class="text-sm text-gray-700">Produk aktif (tampil di website)</label>
            </div>

            <!-- Upload Foto -->
            <div class="col-span-2">
              <label class="label">Foto Produk</label>

              <!-- Preview foto saat ini -->
              <div v-if="currentImageUrl && !newPreview" class="mb-3">
                <img :src="currentImageUrl" class="h-40 rounded-xl object-cover border" />
                <p class="text-xs text-gray-400 mt-1">Foto saat ini — biarkan kosong jika tidak ingin mengubah</p>
              </div>

              <div
                class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center hover:border-green-400 transition-colors cursor-pointer"
                @click="$refs.fileInput.click()"
              >
                <input
                  type="file"
                  accept="image/*"
                  @change="handleImage"
                  class="hidden"
                  ref="fileInput"
                />
                <div v-if="newPreview">
                  <img :src="newPreview" class="mx-auto max-h-40 rounded-lg object-contain mb-2" />
                  <button
                    type="button"
                    @click.stop="clearImage"
                    class="text-red-500 text-xs hover:underline"
                  >
                    ✕ Batalkan ganti foto
                  </button>
                </div>
                <div v-else>
                  <p class="text-3xl mb-1">📸</p>
                  <p class="text-gray-500 text-sm">Klik untuk {{ currentImageUrl ? 'ganti' : 'upload' }} foto</p>
                  <p class="text-gray-400 text-xs mt-1">JPG, PNG, WebP — Maks 2MB</p>
                </div>
              </div>
              <p v-if="form.errors.image" class="error">{{ form.errors.image }}</p>
            </div>
          </div>

          <!-- Tombol -->
          <div class="flex gap-3 pt-2">
            <Link
              :href="route('admin.products.index')"
              class="flex-1 border border-gray-300 text-gray-600 py-2.5 rounded-lg text-sm text-center hover:bg-gray-50"
            >
              Batal
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="flex-1 bg-green-700 hover:bg-green-800 text-white py-2.5 rounded-lg text-sm font-semibold disabled:opacity-60"
            >
              {{ form.processing ? 'Menyimpan...' : '💾 Update Produk' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({ product: Object })

const fileInput  = ref(null)
const newPreview = ref(null)

// image_url untuk preview, image (path) untuk keperluan internal
const currentImageUrl = ref(props.product.image_url)

const form = useForm({
  code:      props.product.code,
  price:     props.product.price,
  weight:    props.product.weight ?? '',
  stock:     props.product.stock,
  is_active: props.product.is_active ?? true,
  image:     null,
  _method:   'PUT',
})

// ── Preview nama & kategori otomatis ──────────────
const previewCategory = computed(() => {
  const p = parseFloat(form.price)
  if (!p) return '—'
  if (p < 3600000)                    return 'Ekonomis'
  if (p >= 3600000 && p < 4200000)  return 'Medium'
  if (p >= 4200000)                    return 'Premium'
  return 'Ekonomis'
})

const previewName = computed(() => {
  if (!form.code) return '—'
  return `Domba ${previewCategory.value} ${form.code}`
})

const categoryColor = (cat) => {
  if (cat === 'Ekonomis') return 'bg-blue-100 text-blue-700'
  if (cat === 'Medium')   return 'bg-yellow-100 text-yellow-700'
  if (cat === 'Premium')  return 'bg-purple-100 text-purple-700'
  return 'bg-gray-100 text-gray-500'
}

// ── Upload foto ───────────────────────────────────
const handleImage = (e) => {
  const file = e.target.files[0]
  if (!file) return
  form.image   = file
  newPreview.value = URL.createObjectURL(file)
}

const clearImage = () => {
  form.image   = null
  newPreview.value = null
  if (fileInput.value) fileInput.value.value = ''
}

// ── Submit ────────────────────────────────────────
const submit = () => {
  form.post(route('admin.products.update', props.product.id), {
    forceFormData: true,
  })
}
</script>

<style scoped>
.label { @apply block text-sm font-medium text-gray-700 mb-1; }
.input { @apply w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none; }
.error { @apply text-red-500 text-xs mt-1; }
</style>