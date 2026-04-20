<template>
    <AdminLayout title="Bulk Update Harga & Berat">
        <div class="max-w-2xl">

            <!-- Panduan -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6 text-sm text-blue-700">
                <p class="font-semibold mb-2">📋 Cara pakai:</p>
                <ol class="list-decimal list-inside space-y-1">
                    <li>Siapkan file Excel dengan kolom: <code class="bg-blue-100 px-1 rounded">Kode</code>, <code class="bg-blue-100 px-1 rounded">Berat</code>, <code class="bg-blue-100 px-1 rounded">Harga</code></li>
                    <li>Isi data sesuai kode produk yang ingin diupdate</li>
                    <li>Upload file — sistem otomatis update harga, berat, dan kategori</li>
                </ol>
                <div class="mt-3 pt-3 border-t border-blue-200">
                    <p class="font-semibold mb-1">Range kategori otomatis:</p>
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <div class="bg-blue-100 rounded px-2 py-1 text-center">
                            <p class="font-medium">Ekonomis</p>
                            <p class="opacity-75">di bawah Rp 3.600.000</p>
                        </div>
                        <div class="bg-blue-100 rounded px-2 py-1 text-center">
                            <p class="font-medium">Medium</p>
                            <p class="opacity-75">Rp 3.720.000 - Rp 4.080.000</p>
                        </div>
                        <div class="bg-blue-100 rounded px-2 py-1 text-center">
                            <p class="font-medium">Premium</p>
                            <p class="opacity-75">di atas Rp 4.200.000</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-8">

                <!-- Download Template -->
                <div class="mb-6 pb-6 border-b border-gray-100">
                    <p class="text-sm font-medium text-gray-700 mb-2">Download template Excel:</p>
                    <button @click="downloadTemplate"
                        class="flex items-center gap-2 text-green-700 border border-green-300 hover:bg-green-50 px-4 py-2 rounded-lg text-sm transition-colors">
                        📥 Download Template
                    </button>
                </div>

                <!-- Upload Area -->
                <div class="border-2 border-dashed rounded-xl p-10 text-center transition-colors cursor-pointer"
                    :class="isDragging ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-green-400'"
                    @dragover.prevent="isDragging = true"
                    @dragleave="isDragging = false"
                    @drop.prevent="handleDrop"
                    @click="$refs.fileInput.click()">
                    <input
                        type="file"
                        accept=".xlsx,.xls,.csv"
                        class="hidden"
                        ref="fileInput"
                        @change="handleFile"
                    />
                    <div v-if="selectedFile">
                        <p class="text-4xl mb-2">📊</p>
                        <p class="text-gray-700 font-medium">{{ selectedFile.name }}</p>
                        <p class="text-gray-400 text-xs mt-1">{{ formatSize(selectedFile.size) }}</p>
                        <button @click.stop="clearFile" class="text-red-500 text-xs hover:underline mt-2 block mx-auto">
                            Hapus file
                        </button>
                    </div>
                    <div v-else>
                        <p class="text-4xl mb-3">📊</p>
                        <p class="text-gray-600 font-medium">Klik atau drag & drop file Excel di sini</p>
                        <p class="text-gray-400 text-sm mt-1">Format: .xlsx, .xls, atau .csv</p>
                    </div>
                </div>

                <!-- Preview data sebelum upload -->
                <div v-if="previewData.length > 0" class="mt-6">
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-sm font-medium text-gray-700">Preview data ({{ previewData.length }} baris):</p>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left px-4 py-2 text-gray-600">Kode</th>
                                    <th class="text-left px-4 py-2 text-gray-600">Berat (kg)</th>
                                    <th class="text-left px-4 py-2 text-gray-600">Harga</th>
                                    <th class="text-left px-4 py-2 text-gray-600">Kategori (otomatis)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="(row, i) in previewData.slice(0, 10)" :key="i" class="hover:bg-gray-50">
                                    <td class="px-4 py-2 font-mono font-medium">{{ row.kode }}</td>
                                    <td class="px-4 py-2">{{ row.berat }} kg</td>
                                    <td class="px-4 py-2">{{ formatRupiah(row.harga) }}</td>
                                    <td class="px-4 py-2">
                                        <span :class="categoryColor(getCategory(row.harga))"
                                            class="px-2 py-0.5 rounded-full text-xs font-medium">
                                            {{ getCategory(row.harga) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p v-if="previewData.length > 10" class="text-xs text-gray-400 text-center py-2">
                            ... dan {{ previewData.length - 10 }} baris lainnya
                        </p>
                    </div>
                </div>

                <!-- Tombol Upload -->
                <div class="mt-6 flex gap-3">
                    <Link :href="route('admin.products.index')"
                        class="flex-1 border border-gray-300 text-gray-600 py-2.5 rounded-lg text-sm text-center hover:bg-gray-50">
                        Batal
                    </Link>
                    <button
                        @click="upload"
                        :disabled="!selectedFile || uploading"
                        class="flex-1 bg-green-700 hover:bg-green-800 text-white py-2.5 rounded-lg text-sm font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ uploading ? 'Mengupdate...' : '🔄 Update Sekarang' }}
                    </button>
                </div>
            </div>

            <!-- Hasil Update -->
            <div v-if="results" class="mt-6 space-y-4">

                <!-- Berhasil -->
                <div v-if="results.updated.length > 0" class="bg-green-50 border border-green-200 rounded-xl p-5">
                    <p class="font-semibold text-green-700 mb-3">✅ Berhasil diupdate ({{ results.updated.length }} produk):</p>
                    <ul class="space-y-1 max-h-48 overflow-y-auto">
                        <li v-for="name in results.updated" :key="name"
                            class="text-sm text-green-600 flex items-center gap-2">
                            <span>•</span> {{ name }}
                        </li>
                    </ul>
                </div>

                <!-- Dilewati -->
                <div v-if="results.skipped.length > 0" class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                    <p class="font-semibold text-yellow-700 mb-3">⚠️ Dilewati ({{ results.skipped.length }} produk) — data kosong:</p>
                    <ul class="space-y-1">
                        <li v-for="name in results.skipped" :key="name"
                            class="text-sm text-yellow-600 flex items-center gap-2">
                            <span>•</span> {{ name }}
                        </li>
                    </ul>
                </div>

                <!-- Gagal -->
                <div v-if="results.failed.length > 0" class="bg-red-50 border border-red-200 rounded-xl p-5">
                    <p class="font-semibold text-red-600 mb-3">❌ Gagal ({{ results.failed.length }}):</p>
                    <ul class="space-y-1">
                        <li v-for="name in results.failed" :key="name"
                            class="text-sm text-red-500 flex items-center gap-2">
                            <span>•</span> {{ name }}
                        </li>
                    </ul>
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
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const fileInput    = ref(null)
const selectedFile = ref(null)
const isDragging   = ref(false)
const uploading    = ref(false)
const previewData  = ref([])
const results      = ref(null)

// Ambil hasil dari flash session
const page = usePage()
watch(() => page.props.flash?.bulk_update_results, (val) => {
    if (val) {
        results.value = val
        uploading.value = false
    }
})

const handleFile = (e) => {
    const file = e.target.files[0]
    if (file) setFile(file)
}

const handleDrop = (e) => {
    isDragging.value = false
    const file = e.dataTransfer.files[0]
    if (file) setFile(file)
}

const setFile = (file) => {
    results.value = null
    selectedFile.value = file
    parsePreview(file)
}

const clearFile = () => {
    selectedFile.value = null
    previewData.value  = []
    if (fileInput.value) fileInput.value.value = ''
}

// Parse CSV untuk preview (hanya untuk CSV)
const parsePreview = (file) => {
    previewData.value = []
    const ext = file.name.split('.').pop().toLowerCase()

    if (ext === 'csv') {
        const reader = new FileReader()
        reader.onload = (e) => {
            const text   = e.target.result
            const lines  = text.split('\n').filter(l => l.trim())
            const header = lines[0].split(',').map(h => h.trim().toLowerCase())

            const rows = []
            for (let i = 1; i < lines.length; i++) {
                const cols = lines[i].split(',')
                if (cols[0]?.trim()) {
                    const row = {}
                    header.forEach((h, idx) => row[h] = cols[idx]?.trim())
                    rows.push({
                        kode:  (row['kode'] || row['code'] || '').toUpperCase(),
                        berat: parseFloat(row['berat'] || 0),
                        harga: parseInt(row['harga'] || 0),
                    })
                }
            }
            previewData.value = rows
        }
        reader.readAsText(file)
    } else {
        // Untuk Excel tidak bisa di-parse di browser tanpa library besar
        // Tampilkan info saja
        previewData.value = []
    }
}

const upload = () => {
    if (!selectedFile.value) return
    uploading.value = true
    results.value   = null

    router.post(route('admin.products.bulk-update.post'), {
        file: selectedFile.value,
    }, {
        forceFormData: true,
        onFinish: () => {
            uploading.value = false
        },
        onError: () => {
            uploading.value = false
        },
    })
}

// Download template Excel (generate CSV sederhana)
const downloadTemplate = () => {
    const csv = 'Kode,Berat,Harga\nKNCR001,33,3960000\nKNCR002,29,3480000\n'
    const blob = new Blob([csv], { type: 'text/csv' })
    const url  = URL.createObjectURL(blob)
    const a    = document.createElement('a')
    a.href     = url
    a.download = 'template_update_harga.csv'
    a.click()
    URL.revokeObjectURL(url)
}

// Helper
const getCategory = (price) => {
    price = parseInt(price)
    if (price < 3600000)                          return 'Ekonomis'
    if (price >= 3720000 && price <= 4080000)     return 'Medium'
    if (price > 4200000)                          return 'Premium'
    return '—'
}

const categoryColor = (cat) => {
    if (cat === 'Ekonomis') return 'bg-blue-100 text-blue-700'
    if (cat === 'Medium')   return 'bg-yellow-100 text-yellow-700'
    if (cat === 'Premium')  return 'bg-purple-100 text-purple-700'
    return 'bg-gray-100 text-gray-500'
}

const formatRupiah = (val) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0,
    }).format(val)

const formatSize = (bytes) => {
    if (bytes < 1024)        return bytes + ' B'
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB'
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB'
}
</script>