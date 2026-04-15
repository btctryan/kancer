<template>
    <AdminLayout title="Bulk Upload Foto">
        <div class="max-w-2xl">

            <!-- Panduan -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6 text-sm text-blue-700">
                <p class="font-semibold mb-2">📋 Cara pakai:</p>
                <ol class="list-decimal list-inside space-y-1">
                    <li>Rename foto sesuai kode produk — contoh: <code
                            class="bg-blue-100 px-1 rounded">KNCR001.jpg</code></li>
                    <li>Pilih semua foto sekaligus (Ctrl+A)</li>
                    <li>Klik Upload — sistem otomatis cocokkan ke produk</li>
                </ol>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-8">

                <!-- Drop Zone -->
                <div class="border-2 border-dashed rounded-xl p-10 text-center transition-colors cursor-pointer"
                    :class="isDragging ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-400'"
                    @dragover.prevent="isDragging = true" @dragleave="isDragging = false" @drop.prevent="handleDrop"
                    @click="$refs.fileInput.click()">
                    <input type="file" multiple accept="image/*" class="hidden" ref="fileInput" @change="handleFiles" />
                    <p class="text-4xl mb-3">📸</p>
                    <p class="text-gray-600 font-medium">Klik atau drag & drop foto di sini</p>
                    <p class="text-gray-400 text-sm mt-1">JPG, PNG, WebP — Bisa pilih banyak sekaligus</p>
                </div>

                <!-- Preview file yang dipilih -->
                <div v-if="selectedFiles.length > 0" class="mt-6">
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-sm font-medium text-gray-700">{{ selectedFiles.length }} foto dipilih:</p>
                        <button @click="clearFiles" class="text-red-500 text-xs hover:underline">Hapus semua</button>
                    </div>
                    <div class="max-h-64 overflow-y-auto space-y-2 pr-1">
                        <div v-for="(file, i) in selectedFiles" :key="i"
                            class="flex items-center gap-3 bg-gray-50 rounded-lg px-4 py-2.5">
                            <img :src="file.preview" class="w-10 h-10 rounded object-cover flex-shrink-0" />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-700 truncate">{{ file.name }}</p>
                                <p class="text-xs text-gray-400">{{ formatSize(file.size) }}</p>
                            </div>
                            <!-- Status cocok/tidak -->
                            <span v-if="file.matched !== null" class="text-xs px-2 py-0.5 rounded-full flex-shrink-0"
                                :class="file.matched ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'">
                                {{ file.matched ? '✓ ' + file.matchedName : '✗ tidak ditemukan' }}
                            </span>
                        </div>
                    </div>

                    <!-- Tombol cek & upload -->
                    <div class="flex gap-3 mt-5">
                        <button @click="checkMatch" :disabled="checking"
                            class="flex-1 border border-gray-300 text-gray-600 py-2.5 rounded-lg text-sm hover:bg-gray-50 disabled:opacity-50">
                            {{ checking ? 'Mengecek...' : '🔍 Cek Kecocokan' }}
                        </button>
                        <button @click="upload" :disabled="uploading || selectedFiles.length === 0"
                            class="flex-1 bg-green-700 hover:bg-green-800 text-white py-2.5 rounded-lg text-sm font-semibold disabled:opacity-50">
                            {{ uploading ? `Mengupload... ${uploadProgress}%` : '⬆️ Upload Semua' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Hasil Upload -->
            <div v-if="results" class="mt-6 space-y-4">
                <!-- Berhasil -->
                <div v-if="results.success.length > 0" class="bg-green-50 border border-green-200 rounded-xl p-5">
                    <p class="font-semibold text-green-700 mb-3">✅ Berhasil ({{ results.success.length }} produk):</p>
                    <ul class="space-y-1">
                        <li v-for="name in results.success" :key="name"
                            class="text-sm text-green-600 flex items-center gap-2">
                            <span>•</span> {{ name }}
                        </li>
                    </ul>
                </div>
                <!-- Gagal -->
                <div v-if="results.failed.length > 0" class="bg-red-50 border border-red-200 rounded-xl p-5">
                    <p class="font-semibold text-red-600 mb-3">❌ Gagal ({{ results.failed.length }} file):</p>
                    <ul class="space-y-1">
                        <li v-for="name in results.failed" :key="name"
                            class="text-sm text-red-500 flex items-center gap-2">
                            <span>•</span> {{ name }}
                        </li>
                    </ul>
                    <p class="text-xs text-red-400 mt-3">Pastikan nama file sama persis dengan kode produk (huruf
                        kapital).</p>
                </div>

                <Link :href="route('admin.products.index')"
                    class="block text-center bg-gray-100 hover:bg-gray-200 text-gray-700 py-2.5 rounded-lg text-sm">
                    ← Kembali ke Daftar Produk
                </Link>
            </div>

        </div>
    </AdminLayout>
</template>

<script setup>
import axios from 'axios'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, usePage } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const fileInput = ref(null)
const selectedFiles = ref([])
const isDragging = ref(false)
const uploading = ref(false)
const checking = ref(false)
const uploadProgress = ref(0)
const results = ref(null)


const formatSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i]
}


// Ambil hasil dari flash session
const page = usePage()
watch(() => page.props.flash?.bulk_results, (val) => {
    if (val) {
        results.value = val
        uploading.value = false
        uploadProgress.value = 0
        clearFiles()
    }
})

const handleFiles = (e) => {
    addFiles(Array.from(e.target.files))
}

const handleDrop = (e) => {
    isDragging.value = false
    addFiles(Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/')))
}

const addFiles = (files) => {
    results.value = null
    selectedFiles.value = files.map(f => ({
        file: f,
        name: f.name,
        size: f.size,
        preview: URL.createObjectURL(f),
        matched: null,
        matchedName: null,
    }))
}

const clearFiles = () => {
    selectedFiles.value = []
    if (fileInput.value) fileInput.value.value = ''
}

// Cek kecocokan nama file dengan kode produk via API
const checkMatch = async () => {
    checking.value = true
    try {
        const codes = selectedFiles.value.map(f =>
            f.name.replace(/\.[^/.]+$/, '').toUpperCase()
        )
        const res = await axios.post('/api/admin/products/check-codes', { codes })
        const data = res.data

        selectedFiles.value = selectedFiles.value.map(f => {
            const code = f.name.replace(/\.[^/.]+$/, '').toUpperCase()
            const match = data[code]
            return { ...f, matched: !!match, matchedName: match || null }
        })
    } catch (e) {
        console.error(e)
    } finally {
        checking.value = false
    }
}

const upload = async () => {
    uploading.value = true
    uploadProgress.value = 0
    results.value = null

    const formData = new FormData()
    selectedFiles.value.forEach(f => formData.append('images[]', f.file))

    try {
        await axios.post(route('admin.products.bulk-upload.post'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
            onUploadProgress: (e) => {
                if (e.lengthComputable) {
                    uploadProgress.value = Math.round((e.loaded / e.total) * 100)
                }
            },
        })
        window.location.href = route('admin.products.bulk-upload')
    } catch (e) {
        console.error(e)
        uploading.value = false
    }
}

</script>