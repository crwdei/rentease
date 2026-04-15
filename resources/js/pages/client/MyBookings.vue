<template>
  <div>

    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-2">My Bookings</h1>
      <p class="text-gray-600">View and manage your rental bookings</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
      <StatCard title="Total" :value="bookings.length" />
      <StatCard title="Active" :value="count('active')" />
      <StatCard title="Pending" :value="count('pending')" />
      <StatCard title="Completed" :value="count('completed')" />
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
      <div class="grid md:grid-cols-2 gap-4">
        <input v-model="search" placeholder="Search..." class="border px-4 py-2 rounded-lg w-full" />
        <select v-model="status" class="border px-4 py-2 rounded-lg w-full">
          <option value="">All</option>
          <option value="pending">Pending</option>
          <option value="active">Active</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-lg p-6">
      <table class="w-full">
        <thead>
          <tr class="bg-gray-50">
            <th class="p-3 text-left">ID</th>
            <th class="p-3 text-left">Property</th>
            <th class="p-3 text-left">Dates</th>
            <th class="p-3 text-left">Amount</th>
            <th class="p-3 text-left">Status</th>
            <th class="p-3 text-left">Actions</th>
          </tr>
        </thead>

        <tbody>
          <tr v-if="filtered.length === 0">
            <td colspan="6" class="text-center py-10 text-gray-400">
              No bookings found
            </td>
          </tr>

          <tr v-for="b in filtered" :key="b.id" class="border-b">
            <td class="p-3">#{{ b.id }}</td>
            <td class="p-3">{{ b.item }}</td>
            <td class="p-3">{{ b.start }} → {{ b.end }}</td>
            <td class="p-3">₱{{ format(b.price) }}</td>
            <td class="p-3">
              <span :class="statusClass(b.status)" class="px-3 py-1 rounded-full text-xs font-medium">
                {{ b.status.toUpperCase() }}
              </span>
            </td>
            <td class="p-3">
              <button @click="view(b)" class="text-purple-500 mr-2">View</button>
              <button v-if="['pending','active'].includes(b.status)" @click="cancel(b.id)" class="text-red-500">
                Cancel
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="selected" class="fixed inset-0 bg-black/30 flex items-center justify-center">
      <div class="bg-white p-6 rounded-xl w-full max-w-lg">
        <h2 class="text-xl font-bold mb-4">Booking Details</h2>

        <div class="space-y-2 text-sm">
          <p><b>ID:</b> #{{ selected.id }}</p>
          <p><b>Property:</b> {{ selected.item }}</p>
          <p><b>Dates:</b> {{ selected.start }} → {{ selected.end }}</p>
          <p><b>Amount:</b> ₱{{ format(selected.price) }}</p>
          <p><b>Status:</b> {{ selected.status }}</p>
        </div>

        <button @click="selected=null" class="mt-4 w-full bg-gray-200 py-2 rounded">
          Close
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue"
import axios from "axios"

const bookings = ref([])
const search = ref('')
const status = ref('')
const selected = ref(null)

onMounted(fetch)

async function fetch() {
  const res = await axios.get('/api/bookings')
  bookings.value = res.data.data.map(b => ({
    id: b.id,
    item: b.rental?.title || '',
    start: b.start_date,
    end: b.end_date,
    price: parseFloat(b.total_price || 0),
    status: b.status
  }))
}

const filtered = computed(() => {
  return bookings.value.filter(b => {
    const matchSearch = b.item.toLowerCase().includes(search.value.toLowerCase())
    const matchStatus = !status.value || b.status === status.value
    return matchSearch && matchStatus
  })
})

function count(type) {
  return bookings.value.filter(b => b.status === type).length
}

function view(b) {
  selected.value = b
}

async function cancel(id) {
  if (!confirm('Cancel booking?')) return
  await axios.delete(`/bookings/${id}`)
  await fetch()
}

function format(v) {
  return Number(v).toLocaleString()
}

function statusClass(s) {
  return {
    'bg-yellow-100 text-yellow-800': s === 'pending',
    'bg-green-100 text-green-800': s === 'active',
    'bg-blue-100 text-blue-800': s === 'completed',
    'bg-gray-100 text-gray-600': s === 'cancelled',
  }
}
</script>