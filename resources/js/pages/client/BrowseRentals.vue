<template>
  <div>
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-2">Available Rentals</h1>
      <p class="text-gray-600">Find the perfect rental for your needs</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input v-model="searchText" placeholder="Search..." class="px-4 py-2 border rounded-lg w-full" />
        <select v-model="typeFilter" class="px-4 py-2 border rounded-lg w-full">
          <option value="">All Types</option>
          <option value="apartment">Apartment</option>
          <option value="vehicle">Vehicle</option>
          <option value="equipment">Equipment</option>
        </select>
        <select v-model="priceFilter" class="px-4 py-2 border rounded-lg w-full">
          <option value="">All Prices</option>
          <option value="0-5000">₱0 - ₱5,000</option>
          <option value="5000-15000">₱5,000 - ₱15,000</option>
          <option value="15000-99999">₱15,000+</option>
        </select>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-lg p-6">
      <table class="w-full">
        <thead>
          <tr class="bg-gray-50">
            <th class="p-3 text-left">Name</th>
            <th class="p-3 text-left">Type</th>
            <th class="p-3 text-left">Description</th>
            <th class="p-3 text-left">Price/Day</th>
            <th class="p-3 text-left">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="filtered.length === 0">
            <td colspan="5" class="text-center py-10 text-gray-400">No rentals found</td>
          </tr>
          <tr v-for="r in filtered" :key="r.id" class="border-b">
            <td class="p-3">{{ r.name }}</td>
            <td class="p-3">{{ capitalize(r.type) }}</td>
            <td class="p-3">{{ r.description }}</td>
            <td class="p-3">₱{{ format(r.price) }}</td>
            <td class="p-3">
              <button @click="openModal(r)" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                Book
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Booking Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center px-4 z-50">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">

        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-700 px-6 py-4">
          <h2 class="text-xl font-bold text-white">Book Rental</h2>
          <p class="text-purple-200 text-sm">{{ selected?.name }}</p>
        </div>

        <div class="p-6 flex flex-col gap-4 max-h-[80vh] overflow-y-auto">

          <!-- GCash Payment Info -->
          <div v-if="selected?.gcash_number" class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <p class="text-sm font-semibold text-blue-700 mb-1">💙 GCash Payment</p>
            <p class="text-sm text-blue-600">Send payment to:</p>
            <p class="text-xl font-bold text-blue-800 tracking-widest">{{ selected.gcash_number }}</p>
            <p class="text-xs text-blue-500 mt-1">{{ selected.company_name }}</p>
          </div>
          <div v-else class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm text-gray-400 italic">
            No GCash number set by lessor yet.
          </div>

          <!-- Date Pickers -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label>
              <input v-model="form.startDate" type="date" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">End Date</label>
              <input v-model="form.endDate" type="date" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400" />
            </div>
          </div>

          <!-- Price Summary -->
          <div v-if="totalDays > 0" class="bg-purple-50 border border-purple-200 rounded-xl p-4">
            <div class="flex justify-between text-sm text-purple-700">
              <span>₱{{ format(selected?.price) }} × {{ totalDays }} day{{ totalDays > 1 ? 's' : '' }}</span>
              <span class="font-bold text-purple-900">₱{{ format(totalPrice) }}</span>
            </div>
          </div>

          <!-- Contact Info -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Full Name</label>
            <input v-model="form.name" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400" />
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
            <input v-model="form.email" type="email" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400" />
          </div>
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Contact Number</label>
            <input v-model="form.contact" type="text" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400" />
          </div>

          <!-- GCash Payment Proof -->
          <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex flex-col gap-3">
            <p class="text-sm font-semibold text-gray-700">💸 GCash Payment Confirmation</p>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">GCash Reference Number</label>
              <input v-model="form.gcash_reference" type="text" placeholder="e.g. 1234567890" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400" />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-600 mb-1">Amount Sent (₱)</label>
              <input v-model="form.gcash_amount" type="number" placeholder="e.g. 5000" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400" />
            </div>
          </div>

          <!-- Notes -->
          <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Notes <span class="text-gray-400">(optional)</span></label>
            <textarea v-model="form.notes" rows="2" placeholder="Any special requests..." class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 resize-none"></textarea>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-2">
            <button
              @click="closeModal"
              class="flex-1 border border-gray-300 text-gray-600 py-2 rounded-lg font-medium hover:bg-gray-50 transition"
            >
              Cancel
            </button>
            <button
              @click="submitBooking"
              :disabled="busy"
              class="flex-1 bg-gradient-to-r from-purple-500 to-purple-700 text-white py-2 rounded-lg font-medium hover:-translate-y-0.5 transition disabled:opacity-50"
            >
              {{ busy ? 'Booking...' : 'Confirm Booking' }}
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const rentals = ref([])
const searchText = ref('')
const typeFilter = ref('')
const priceFilter = ref('')
const showModal = ref(false)
const selected = ref(null)
const busy = ref(false)

const form = ref({
  name: '',
  email: '',
  contact: '',
  startDate: '',
  endDate: '',
  gcash_reference: '',
  gcash_amount: '',
  notes: '',
})

onMounted(fetchRentals)

async function fetchRentals() {
  const res = await axios.get('/api/rentals')
  rentals.value = res.data.data.map(r => ({
    id: r.id,
    name: r.title,
    type: r.type,
    description: r.description,
    price: parseFloat(r.price_per_day || 0),
gcash_number: r.lessor?.gcash_number ?? null,
company_name: r.lessor?.name ?? '',
  }))
}

const filtered = computed(() => {
  return rentals.value.filter(r => {
    const matchSearch = r.name.toLowerCase().includes(searchText.value.toLowerCase())
    const matchType = !typeFilter.value || r.type === typeFilter.value
    return matchSearch && matchType
  })
})

const totalDays = computed(() => {
  if (!form.value.startDate || !form.value.endDate) return 0
  const start = new Date(form.value.startDate)
  const end = new Date(form.value.endDate)
  const diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24))
  return diff > 0 ? diff : 0
})

const totalPrice = computed(() => {
  return totalDays.value * (selected.value?.price ?? 0)
})

function openModal(r) {
  selected.value = r
  showModal.value = true
  form.value = { name: '', email: '', contact: '', startDate: '', endDate: '', gcash_reference: '', gcash_amount: '', notes: '' }
}

function closeModal() {
  showModal.value = false
  selected.value = null
}

async function submitBooking() {
  busy.value = true
  try {
    await axios.post('/bookings', {
      rental_id:       selected.value.id,
      customer_name:   form.value.name,
      customer_email:  form.value.email,
      customer_phone:  form.value.contact,
      start_date:      form.value.startDate,
      end_date:        form.value.endDate,
      total_price:     totalPrice.value,
      gcash_reference: form.value.gcash_reference,
      gcash_amount:    form.value.gcash_amount,
      notes:           form.value.notes,
    })
    alert('Booking submitted!')
    closeModal()
  } catch (e) {
    alert(e.response?.data?.message ?? 'Something went wrong.')
  } finally {
    busy.value = false
  }
}

function capitalize(v) {
  return v?.charAt(0).toUpperCase() + v?.slice(1)
}

function format(v) {
  return Number(v).toLocaleString()
}
</script>