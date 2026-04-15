<template>
  <AdminLayout title="Pesanan (Keranjang Aktif)">
    <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
      ℹ️ Pesanan ditampilkan dari keranjang aktif user. Checkout dilakukan via WhatsApp.
    </div>

    <div class="space-y-4">
      <div v-for="order in orders.data" :key="order.id"
        class="bg-white rounded-xl shadow-sm overflow-hidden"
      >
        <!-- Header User -->
        <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-green-100 rounded-full flex items-center justify-center font-bold text-green-700">
              {{ order.name[0].toUpperCase() }}
            </div>
            <div>
              <p class="font-semibold text-gray-800">{{ order.name }}</p>
              <p class="text-xs text-gray-400">{{ order.email }}</p>
            </div>
          </div>
          <a v-if="order.phone"
            :href="`https://wa.me/${order.phone}?text=${waText(order)}`"
            target="_blank"
            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-xs font-medium flex items-center gap-2"
          >
            💬 Hubungi via WA
          </a>
        </div>

        <!-- Items -->
        <div class="divide-y divide-gray-50">
          <div v-for="cart in order.carts" :key="cart.id"
            class="px-6 py-3 flex justify-between items-center"
          >
            <div class="flex items-center gap-3">
              <img v-if="cart.product?.image" :src="`/storage/${cart.product.image}`"
                class="w-12 h-12 rounded-lg object-cover" />
              <div v-else class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center text-xl">🐑</div>
              <div>
                <p class="text-sm font-medium text-gray-700">{{ cart.product?.name }}</p>
                <p class="text-xs text-gray-400">{{ cart.quantity }} ekor × {{ formatRupiah(cart.product?.price) }}</p>
              </div>
            </div>
            <p class="font-semibold text-green-700 text-sm">
              {{ formatRupiah((cart.product?.price ?? 0) * cart.quantity) }}
            </p>
          </div>
        </div>

        <!-- Total -->
        <div class="px-6 py-3 bg-gray-50 border-t flex justify-end">
          <p class="font-bold text-gray-800">
            Total: {{ formatRupiah(calcTotal(order.carts)) }}
          </p>
        </div>
      </div>

      <!-- Empty state -->
      <div v-if="orders.data.length === 0" class="text-center py-16 text-gray-400">
        <p class="text-4xl mb-3">📭</p>
        <p>Belum ada pesanan aktif</p>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="orders.last_page > 1" class="mt-6 flex justify-center gap-2 text-sm">
      <Link v-if="orders.prev_page_url" :href="orders.prev_page_url" class="px-4 py-2 border rounded hover:bg-gray-50">← Prev</Link>
      <Link v-if="orders.next_page_url" :href="orders.next_page_url" class="px-4 py-2 border rounded hover:bg-gray-50">Next →</Link>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'

defineProps({ orders: Object })

const formatRupiah = (val) =>
  new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val ?? 0)

const calcTotal = (carts) =>
  carts.reduce((sum, c) => sum + (c.product?.price ?? 0) * c.quantity, 0)

const waText = (order) => {
  const items = order.carts.map(c =>
    `- ${c.product?.name} x${c.quantity} = ${formatRupiah((c.product?.price ?? 0) * c.quantity)}`
  ).join('\n')
  return encodeURIComponent(
    `Halo ${order.name}, berikut ringkasan pesanan Anda:\n\n${items}\n\nTotal: ${formatRupiah(calcTotal(order.carts))}`
  )
}
</script>