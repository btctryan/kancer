<template>
  <AdminLayout title="Manajemen Produk">
    <div class="flex justify-between items-center mb-6">
      <p class="text-gray-500 text-sm">Total: {{ products.total }} produk</p>
      <Link :href="route('admin.products.bulk-upload')"
        class="border border-green-700 text-green-700 hover:bg-green-50 px-5 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
        📸 Bulk Upload Foto
      </Link>
      <Link :href="route('admin.products.create')"
        class="bg-green-700 hover:bg-green-800 text-white px-5 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
        ➕ Tambah Produk
      </Link>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
          <tr>
            <th class="text-left px-6 py-4 text-gray-600 font-semibold">Produk</th>
            <th class="text-left px-6 py-4 text-gray-600 font-semibold">Kategori</th>
            <th class="text-left px-6 py-4 text-gray-600 font-semibold">Harga</th>
            <th class="text-left px-6 py-4 text-gray-600 font-semibold">Stok</th>
            <th class="text-left px-6 py-4 text-gray-600 font-semibold">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <img v-if="product.image" :src="`/storage/${product.image}`"
                  class="w-12 h-12 rounded-lg object-cover" />
                <div v-else class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-2xl">🐑</div>
                <div>
                  <p class="font-medium text-gray-800">{{ product.name }}</p>
                  <p class="text-xs text-gray-400 truncate max-w-xs">{{ product.description?.substring(0, 60) }}...</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs">
                {{ product.category ?? 'Umum' }}
              </span>
            </td>
            <td class="px-6 py-4 font-semibold text-green-700">{{ formatRupiah(product.price) }}</td>
            <td class="px-6 py-4">
              <span :class="product.stock > 0 ? 'text-gray-700' : 'text-red-600 font-semibold'">
                {{ product.stock > 0 ? product.stock + ' ekor' : 'Habis' }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">
                <Link :href="route('admin.products.edit', product.id)"
                  class="bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1.5 rounded-lg text-xs font-medium">✏️ Edit
                </Link>
                <button @click="confirmDelete(product)"
                  class="bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg text-xs font-medium">🗑️
                  Hapus</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div class="px-6 py-4 border-t flex justify-between items-center text-sm text-gray-500">
        <span>Halaman {{ products.current_page }} dari {{ products.last_page }}</span>
        <div class="flex gap-2">
          <Link v-if="products.prev_page_url" :href="products.prev_page_url"
            class="px-3 py-1 border rounded hover:bg-gray-50">← Prev</Link>
          <Link v-if="products.next_page_url" :href="products.next_page_url"
            class="px-3 py-1 border rounded hover:bg-gray-50">Next →</Link>
        </div>
      </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div v-if="deleteTarget" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 shadow-xl max-w-sm w-full mx-4">
        <p class="font-semibold text-gray-800 mb-2">Hapus Produk?</p>
        <p class="text-gray-500 text-sm mb-6">
          Produk <strong>{{ deleteTarget.name }}</strong> akan dihapus permanen beserta fotonya.
        </p>
        <div class="flex gap-3">
          <button @click="deleteTarget = null"
            class="flex-1 border border-gray-300 text-gray-600 py-2 rounded-lg text-sm hover:bg-gray-50">
            Batal
          </button>
          <button @click="doDelete"
            class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg text-sm font-medium">
            Ya, Hapus
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

defineProps({ products: Object })

const deleteTarget = ref(null)
const confirmDelete = (product) => deleteTarget.value = product
const doDelete = () => {
  router.delete(route('admin.products.destroy', deleteTarget.value.id), {
    onFinish: () => deleteTarget.value = null
  })
}

const formatRupiah = (val) =>
  new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val)
</script>