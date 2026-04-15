<template>
  <div>
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-2">Admin Dashboard</h1>
      <p class="text-gray-600">Manage your rental business efficiently</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
      <div class="bg-white rounded-xl shadow-lg p-6 hover:-translate-y-1 transition-transform">
        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-2xl mb-4">
          🏢
        </div>
        <div class="text-3xl font-bold text-gray-800 mb-1">{{ totalRentals }}</div>
        <div class="text-sm text-gray-600">Total Rentals</div>
      </div>

      <div class="bg-white rounded-xl shadow-lg p-6 hover:-translate-y-1 transition-transform">
        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-2xl mb-4">
          ✅
        </div>
        <div class="text-3xl font-bold text-gray-800 mb-1">{{ availableRentals }}</div>
        <div class="text-sm text-gray-600">Available</div>
      </div>

      <div class="bg-white rounded-xl shadow-lg p-6 hover:-translate-y-1 transition-transform">
        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-2xl mb-4">
          🔴
        </div>
        <div class="text-3xl font-bold text-gray-800 mb-1">{{ rentedRentals }}</div>
        <div class="text-sm text-gray-600">Currently Rented</div>
      </div>

      <div class="bg-white rounded-xl shadow-lg p-6 hover:-translate-y-1 transition-transform">
        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-2xl mb-4">
          💰
        </div>
        <div class="text-3xl font-bold text-gray-800 mb-1">₱{{ formatMoney(totalRevenue) }}</div>
        <div class="text-sm text-gray-600">Total Revenue</div>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <router-link
        to="/companies"
        class="bg-white rounded-xl shadow-lg p-6 text-center hover:-translate-y-1 hover:shadow-xl transition-all"
      >
        <div class="text-3xl mb-2">🏢</div>
        <div class="font-medium text-gray-800">Manage Companies</div>
      </router-link>

      <router-link
        to="/rentals"
        class="bg-white rounded-xl shadow-lg p-6 text-center hover:-translate-y-1 hover:shadow-xl transition-all"
      >
        <div class="text-3xl mb-2">➕</div>
        <div class="font-medium text-gray-800">Add Rental</div>
      </router-link>

      <router-link
        to="/bookings"
        class="bg-white rounded-xl shadow-lg p-6 text-center hover:-translate-y-1 hover:shadow-xl transition-all"
      >
        <div class="text-3xl mb-2">📋</div>
        <div class="font-medium text-gray-800">View Bookings</div>
      </router-link>

      <router-link
        to="/customers"
        class="bg-white rounded-xl shadow-lg p-6 text-center hover:-translate-y-1 hover:shadow-xl transition-all"
      >
        <div class="text-3xl mb-2">👥</div>
        <div class="font-medium text-gray-800">View Customers</div>
      </router-link>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
      <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Rental Types Breakdown</h3>
        <div class="space-y-3">
          <div class="flex justify-between py-3 border-b border-gray-100">
            <span class="text-gray-600">Apartments</span>
            <span class="font-semibold text-gray-800">{{ apartmentCount }}</span>
          </div>
          <div class="flex justify-between py-3 border-b border-gray-100">
            <span class="text-gray-600">Vehicles</span>
            <span class="font-semibold text-gray-800">{{ vehicleCount }}</span>
          </div>
          <div class="flex justify-between py-3">
            <span class="text-gray-600">Equipment</span>
            <span class="font-semibold text-gray-800">{{ equipmentCount }}</span>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Booking Status</h3>
        <div class="space-y-3">
          <div class="flex justify-between py-3 border-b border-gray-100">
            <span class="text-gray-600">Active Bookings</span>
            <span class="font-semibold text-gray-800">{{ activeBookings }}</span>
          </div>
          <div class="flex justify-between py-3 border-b border-gray-100">
            <span class="text-gray-600">Pending</span>
            <span class="font-semibold text-gray-800">{{ pendingBookings }}</span>
          </div>
          <div class="flex justify-between py-3">
            <span class="text-gray-600">Completed</span>
            <span class="font-semibold text-gray-800">{{ completedBookings }}</span>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">System Summary</h3>
        <div class="space-y-3">
          <div class="flex justify-between py-3 border-b border-gray-100">
            <span class="text-gray-600">Total Companies</span>
            <span class="font-semibold text-gray-800">{{ totalCompanies }}</span>
          </div>
          <div class="flex justify-between py-3 border-b border-gray-100">
            <span class="text-gray-600">Total Customers</span>
            <span class="font-semibold text-gray-800">{{ totalCustomers }}</span>
          </div>
          <div class="flex justify-between py-3">
            <span class="text-gray-600">Total Bookings</span>
            <span class="font-semibold text-gray-800">{{ totalBookings }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Recent Bookings</h2>
        <router-link
          to="/bookings"
          class="text-purple-500 font-medium hover:text-purple-700 transition-colors"
        >
          View All →
        </router-link>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-gray-50">
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Customer</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Item</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Start Date</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">End Date</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="recentBookings.length === 0">
              <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                No bookings yet
              </td>
            </tr>

            <tr
              v-for="booking in recentBookings"
              :key="booking.id"
              class="border-b border-gray-100 hover:bg-gray-50 transition-colors"
            >
              <td class="px-4 py-3">#{{ booking.id }}</td>
              <td class="px-4 py-3">{{ booking.customer }}</td>
              <td class="px-4 py-3">{{ booking.item }}</td>
              <td class="px-4 py-3">{{ booking.start }}</td>
              <td class="px-4 py-3">{{ booking.end }}</td>
              <td class="px-4 py-3">₱{{ formatMoney(booking.price) }}</td>
              <td class="px-4 py-3">
                <span
                  class="px-3 py-1 rounded-full text-xs font-medium"
                  :class="statusClass(booking.status)"
                >
                  {{ String(booking.status || '').toUpperCase() }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="loading" class="mt-4 text-sm text-gray-500">
      Loading dashboard...
    </div>

    <div v-if="errorMessage" class="mt-4 text-sm text-red-600">
      {{ errorMessage }}
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";

const loading = ref(true);
const errorMessage = ref("");

const totalRentals = ref(0);
const availableRentals = ref(0);
const rentedRentals = ref(0);
const totalRevenue = ref(0);
const apartmentCount = ref(0);
const vehicleCount = ref(0);
const equipmentCount = ref(0);
const activeBookings = ref(0);
const pendingBookings = ref(0);
const completedBookings = ref(0);
const totalCompanies = ref(0);
const totalCustomers = ref(0);
const totalBookings = ref(0);
const recentBookings = ref([]);

function formatMoney(value) {
  return Number(value || 0).toLocaleString();
}

function statusClass(status) {
  if (status === "active") return "bg-red-100 text-red-800";
  if (status === "pending") return "bg-yellow-100 text-yellow-800";
  if (status === "completed") return "bg-blue-100 text-blue-800";
  return "bg-gray-100 text-gray-800";
}

async function fetchStats() {
  try {
    const { data } = await axios.get("/admin/api/dashboard/stats");

    totalRentals.value = data?.totals?.totalRentals ?? 0;
    availableRentals.value = data?.totals?.availableRentals ?? 0;
    rentedRentals.value = data?.totals?.rentedRentals ?? 0;     
    totalRevenue.value = data?.totals?.totalRevenue ?? 0;
    apartmentCount.value = data?.totals?.apartmentCount ?? 0;
    vehicleCount.value = data?.totals?.vehicleCount ?? 0;
    equipmentCount.value = data?.totals?.equipmentCount ?? 0;
    activeBookings.value = data?.totals?.activeBookings ?? 0;
    pendingBookings.value = data?.totals?.pendingBookings ?? 0;
    completedBookings.value = data?.totals?.completedBookings ?? 0;
    totalCompanies.value = data?.totals?.totalCompanies ?? 0;
    totalCustomers.value = data?.totals?.totalCustomers ?? 0;
    totalBookings.value = data?.totals?.totalBookings ?? 0;
    recentBookings.value = data?.recentBookings ?? [];
  } catch (error) {
    errorMessage.value = "Failed to load dashboard stats.";
    console.error(error);
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  fetchStats();
});
</script>