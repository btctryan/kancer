<template>
    <AdminLayout title="Tambah Produk">
        <div class="max-w-2xl">
            <div class="bg-white rounded-xl shadow-sm p-8">
                <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-5">
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
                            <p class="text-xs text-gray-400 mt-1">
                                Kode otomatis: <strong>{{ nextCode }}</strong> — atau isi manual
                            </p>
                            <p v-if="form.errors.code" class="error">{{ form.errors.code }}</p>
                        </div>

                        <!-- Preview Nama Otomatis -->
                        <div class="col-span-2 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
                            <p class="text-xs text-green-600 mb-1">Nama produk akan otomatis menjadi:</p>
                            <p class="font-semibold text-green-800 text-sm">{{ previewName }}</p>
                        </div>

                        <!-- Harga -->
                        <div>
                            <label class="label">Harga (Rp) *</label>
                            <input
                                v-model="form.price"
                                type="number"
                                placeholder="2500000"
                                class="input"
                                :class="{ 'border-red-400': form.errors.price }"
                            />
                            <p v-if="form.errors.price" class="error">{{ form.errors.price }}</p>
                        </div>

                        <!-- Preview Kategori -->
                        <div>
                            <label class="label">Kategori (otomatis)</label>
                            <div class="input bg-gray-50 flex items-center">
                                <span :class="categoryColor" class="px-2 py-0.5 rounded-full text-xs font-medium">
                                    {{ previewCategory }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Ditentukan otomatis dari harga</p>
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
                                placeholder="1"
                                class="input"
                                :class="{ 'border-red-400': form.errors.stock }"
                            />
                            <p v-if="form.errors.stock" class="error">{{ form.errors.stock }}</p>
                        </div>

                        <!-- Info Range Harga -->
                        <div class="col-span-2 bg-gray-50 border border-gray-200 rounded-lg px-4 py-3">
                            <p class="text-xs text-gray-500 font-medium mb-2">Range harga per kategori:</p>
                            <div class="grid grid-cols-3 gap-2 text-xs">
                                <div class="text-center">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Ekonomis</span>
                                    <p class="text-gray-500 mt-1">di bawah Rp 3.600.000</p>
                                </div>
                                <div class="text-center">
                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Medium</span>
                                    <p class="text-gray-500 mt-1">Rp 3.600.000 - Rp 4.200.000</p>
                                </div>
                                <div class="text-center">
                                    <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">Premium</span>
                                    <p class="text-gray-500 mt-1">di atas Rp 4.200.000</p>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Foto -->
                        <div class="col-span-2">
                            <label class="label">Foto Produk</label>
                            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-green-400 transition-colors">
                                <input
                                    type="file"
                                    accept="image/*"
                                    @change="handleImage"
                                    class="hidden"
                                    ref="fileInput"
                                />
                                <div v-if="preview">
                                    <img :src="preview" class="mx-auto max-h-48 rounded-lg object-contain mb-3" />
                                    <button type="button" @click="clearImage" class="text-red-500 text-sm hover:underline">
                                        Hapus foto
                                    </button>
                                </div>
                                <div v-else @click="$refs.fileInput.click()" class="cursor-pointer">
                                    <p class="text-4xl mb-2">📸</p>
                                    <p class="text-gray-500 text-sm">Klik untuk upload foto</p>
                                    <p class="text-gray-400 text-xs mt-1">JPG, PNG, WebP — Maks 2MB</p>
                                </div>
                            </div>
                            <p v-if="form.errors.image" class="error">{{ form.errors.image }}</p>
                        </div>

                    </div>

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
                            {{ form.processing ? 'Menyimpan...' : '💾 Simpan Produk' }}
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

const props = defineProps({
    next_code: String,
})

const fileInput = ref(null)
const preview = ref(null)

const form = useForm({
    code:   props.next_code || '',
    price:  '',
    weight: '',
    stock:  '',
    image:  null,
})

// ─── Preview kategori otomatis ─────────────────────
const previewCategory = computed(() => {
    const price = parseFloat(form.price)
    if (!price) return '—'
    if (price < 3600000) return 'Ekonomis'
    if (price >= 3720000 && price <= 4080000) return 'Medium'
    if (price > 4200000) return 'Premium'
    return '—'
})

const categoryColor = computed(() => {
    const cat = previewCategory.value
    if (cat === 'Ekonomis') return 'bg-blue-100 text-blue-700'
    if (cat === 'Medium')   return 'bg-yellow-100 text-yellow-700'
    if (cat === 'Premium')  return 'bg-purple-100 text-purple-700'
    return 'bg-gray-100 text-gray-500'
})

// ─── Preview nama otomatis ─────────────────────────
const previewName = computed(() => {
    const code = form.code || '???'
    const cat  = previewCategory.value
    if (cat === '—') return `Domba ??? ${code}`
    return `Domba ${cat} ${code}`
})

const nextCode = computed(() => props.next_code || 'KNCR001')

// ─── Handle foto ───────────────────────────────────
const handleImage = (e) => {
    const file = e.target.files[0]
    if (!file) return
    form.image = file
    preview.value = URL.createObjectURL(file)
}

const clearImage = () => {
    form.image = null
    preview.value = null
    if (fileInput.value) fileInput.value.value = ''
}

const submit = () => {
    form.post(route('admin.products.store'), { forceFormData: true })
}
</script>

<style scoped>
.label { @apply block text-sm font-medium text-gray-700 mb-1; }
.input { @apply w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none; }
.error { @apply text-red-500 text-xs mt-1; }
</style>