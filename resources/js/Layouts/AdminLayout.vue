<template>
  <div class="flex h-screen bg-gray-100 font-sans">
    <!-- Sidebar -->
    <aside class="w-64 bg-green-900 text-white flex flex-col shadow-xl">
      <!-- Logo -->
      <div class="p-6 border-b border-green-800">
        <h1 class="text-2xl font-bold tracking-wide">🐑 KANCER</h1>
        <p class="text-green-300 text-xs mt-1">Admin Panel</p>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 p-4 space-y-1">
        <Link
          v-for="item in navItems"
          :key="item.route"
          :href="route(item.route)"
          class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors"
          :class="isActive(item.route)
            ? 'bg-green-700 text-white'
            : 'text-green-200 hover:bg-green-800 hover:text-white'"
        >
          <span class="text-lg">{{ item.icon }}</span>
          {{ item.label }}
        </Link>
      </nav>

      <!-- Admin Info + Logout -->
      <div class="p-4 border-t border-green-800">
        <p class="text-xs text-green-400 mb-2">Login sebagai:</p>
        <p class="text-sm font-semibold truncate">{{ $page.props.auth?.admin?.name ?? 'Admin' }}</p>
        <form @submit.prevent="logout" class="mt-3">
          <button
            type="submit"
            class="w-full text-left text-sm text-red-300 hover:text-red-100 flex items-center gap-2"
          >
            <span>🚪</span> Logout
          </button>
        </form>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Topbar -->
      <header class="bg-white shadow-sm px-8 py-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-700">{{ title }}</h2>
        <span class="text-sm text-gray-400">{{ currentDate }}</span>
      </header>

      <!-- Flash Message -->
      <div v-if="$page.props.flash?.success" class="mx-8 mt-4">
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
          <span>✅</span> {{ $page.props.flash.success }}
        </div>
      </div>

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto p-8">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

defineProps({ title: { type: String, default: 'Dashboard' } })

const navItems = [
  { route: 'admin.dashboard',        icon: '📊', label: 'Dashboard' },
  { route: 'admin.products.index',   icon: '🐑', label: 'Produk' },
  { route: 'admin.users.index',      icon: '👥', label: 'User' },
  { route: 'admin.orders.index',     icon: '📦', label: 'Pesanan' },
]

const page = usePage()

const isActive = (routeName) => {
  return page.url.startsWith('/' + routeName.replace('admin.', 'admin/').replace('.', '/'))
}

const logout = () => router.post(route('admin.logout'))

const currentDate = computed(() => {
  return new Date().toLocaleDateString('id-ID', {
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
  })
})
</script>