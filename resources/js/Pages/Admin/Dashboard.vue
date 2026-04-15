<template>
  <AdminLayout title="Dashboard">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 mb-8">
      <div v-for="stat in statCards" :key="stat.label"
        class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4 border-l-4"
        :class="stat.border"
      >
        <div class="text-3xl">{{ stat.icon }}</div>
        <div>
          <p class="text-2xl font-bold text-gray-800">{{ stat.value }}</p>
          <p class="text-xs text-gray-500">{{ stat.label }}</p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Produk Terbaru -->
      <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="font-semibold text-gray-700">Produk Terbaru</h3>
          <Link :href="route('admin.products.index')" class="text-green-600 text-sm hover:underline">Lihat semua →</Link>
        </div>
        <div class="space-y-3">
          <div v-for="p in recent_products" :key="p.id"
            class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0"
          >
            <div>
              <p class="text-sm font-medium text-gray-700">{{ p.name }}</p>
              <p class="text-xs text-gray-400">Stok: {{ p.stock }}</p>
            </div>
            <p class="text-sm font-semibold text-green-700">{{ formatRupiah(p.price) }}</p>
          </div>
        </div>
      </div>

      <!-- User Terbaru -->
      <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="font-semibold text-gray-700">User Terbaru</h3>
          <Link :href="route('admin.users.index')" class="text-green-600 text-sm hover:underline">Lihat semua →</Link>
        </div>
        <div class="space-y-3">
          <div v-for="u in recent_users" :key="u.id"
            class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0"
          >
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-sm font-bold text-green-700">
              {{ u.name[0].toUpperCase() }}
            </div>
            <div>
              <p class="text-sm font-medium text-gray-700">{{ u.name }}</p>
              <p class="text-xs text-gray-400">{{ u.email }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  stats: Object,
  recent_products: Array,
  recent_users: Array,
})

const statCards = computed(() => [
  { label: 'Total Produk',    value: props.stats.total_products,   icon: '🐑', border: 'border-green-500' },
  { label: 'Total User',      value: props.stats.total_users,       icon: '👥', border: 'border-blue-500'  },
  { label: 'Item di Keranjang', value: props.stats.total_cart_items, icon: '🛒', border: 'border-yellow-500' },
  { label: 'User Berminat',   value: props.stats.users_with_cart,  icon: '💬', border: 'border-purple-500' },
  { label: 'Stok Habis',      value: props.stats.out_of_stock,     icon: '⚠️', border: 'border-red-500'   },
])

const formatRupiah = (val) =>
  new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val)
</script>