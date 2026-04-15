<template>
  <AdminLayout title="Manajemen User">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b flex justify-between items-center">
        <p class="text-gray-500 text-sm">Total: <strong>{{ users.total }}</strong> user terdaftar</p>
      </div>
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
          <tr>
            <th class="text-left px-6 py-4 text-gray-600 font-semibold">User</th>
            <th class="text-left px-6 py-4 text-gray-600 font-semibold">No. WhatsApp</th>
            <th class="text-left px-6 py-4 text-gray-600 font-semibold">Item di Keranjang</th>
            <th class="text-left px-6 py-4 text-gray-600 font-semibold">Bergabung</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-green-100 rounded-full flex items-center justify-center font-bold text-green-700">
                  {{ user.name[0].toUpperCase() }}
                </div>
                <div>
                  <p class="font-medium text-gray-800">{{ user.name }}</p>
                  <p class="text-xs text-gray-400">{{ user.email }}</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-gray-600">
              <a v-if="user.phone" :href="`https://wa.me/${user.phone}`" target="_blank"
                class="text-green-600 hover:underline flex items-center gap-1">
                📱 {{ user.phone }}
              </a>
              <span v-else class="text-gray-300">-</span>
            </td>
            <td class="px-6 py-4">
              <span v-if="user.carts_count > 0"
                class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-xs font-medium">
                {{ user.carts_count }} item
              </span>
              <span v-else class="text-gray-300 text-xs">kosong</span>
            </td>
            <td class="px-6 py-4 text-gray-400 text-xs">{{ formatDate(user.created_at) }}</td>
          </tr>
        </tbody>
      </table>
      <div class="px-6 py-4 border-t flex justify-between items-center text-sm text-gray-500">
        <span>Halaman {{ users.current_page }} dari {{ users.last_page }}</span>
        <div class="flex gap-2">
          <Link v-if="users.prev_page_url" :href="users.prev_page_url" class="px-3 py-1 border rounded hover:bg-gray-50">← Prev</Link>
          <Link v-if="users.next_page_url" :href="users.next_page_url" class="px-3 py-1 border rounded hover:bg-gray-50">Next →</Link>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'

defineProps({ users: Object })

const formatDate = (d) => new Date(d).toLocaleDateString('id-ID', {
  day: 'numeric', month: 'short', year: 'numeric'
})
</script>