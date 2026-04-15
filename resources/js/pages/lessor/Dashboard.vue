<template>
  <div>
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-2">Lessor Dashboard</h1>
      <p class="text-gray-600 mb-4">Welcome back! Here's what's happening with your properties</p>

      <div
        v-if="companyName"
        class="inline-block bg-gradient-to-r from-purple-500 to-purple-700 text-white px-4 py-2 rounded-lg font-medium"
      >
        {{ companyName }}
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
      <div class="bg-white rounded-xl shadow-lg p-6 hover:-translate-y-1 transition-transform">
        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-2xl mb-4">
          🏢
        </div>
        <div class="text-3xl font-bold text-gray-800 mb-1">{{ myRentals }}</div>
        <div class="text-sm text-gray-600">My Properties</div>
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
          📋
        </div>
        <div class="text-3xl font-bold text-gray-800 mb-1">{{ activeBookings }}</div>
        <div class="text-sm text-gray-600">Active Bookings</div>
      </div>

      <div class="bg-white rounded-xl shadow-lg p-6 hover:-translate-y-1 transition-transform">
        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-2xl mb-4">
          💰
        </div>
        <div class="text-3xl font-bold text-gray-800 mb-1">₱{{ formatMoney(myRevenue) }}</div>
        <div class="text-sm text-gray-600">Total Revenue</div>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
      <router-link
        to="/my-properties"
        class="bg-white rounded-xl shadow-lg p-6 text-center hover:-translate-y-1 hover:shadow-xl transition-all"
      >
        <div class="text-3xl mb-2">➕</div>
        <div class="font-medium text-gray-800">Add Property</div>
      </router-link>

      <router-link
        to="/bookings"
        class="bg-white rounded-xl shadow-lg p-6 text-center hover:-translate-y-1 hover:shadow-xl transition-all"
      >
        <div class="text-3xl mb-2">👁️</div>
        <div class="font-medium text-gray-800">View Bookings</div>
      </router-link>

      <router-link
        to="/reports"
        class="bg-white rounded-xl shadow-lg p-6 text-center hover:-translate-y-1 hover:shadow-xl transition-all"
      >
        <div class="text-3xl mb-2">📊</div>
        <div class="font-medium text-gray-800">View Reports</div>
      </router-link>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
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
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Property</th>
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
                  {{ booking.status.toUpperCase() }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">My Properties</h2>
        <router-link
          to="/my-properties"
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
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Property Name</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Price</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total Bookings</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="properties.length === 0">
              <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                No properties yet
              </td>
            </tr>

            <tr
              v-for="property in properties"
              :key="property.id"
              class="border-b border-gray-100 hover:bg-gray-50 transition-colors"
            >
              <td class="px-4 py-3">#{{ property.id }}</td>
              <td class="px-4 py-3">{{ property.name }}</td>
              <td class="px-4 py-3">{{ property.typeLabel }}</td>
              <td class="px-4 py-3">₱{{ formatMoney(property.price) }}</td>
              <td class="px-4 py-3">
                <span
                  class="px-3 py-1 rounded-full text-xs font-medium"
                  :class="property.status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                >
                  {{ property.status.toUpperCase() }}
                </span>
              </td>
              <td class="px-4 py-3">{{ property.bookings }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="loading" class="mt-4 text-sm text-gray-500">Loading dashboard...</div>
  </div>
</template>

<script setup>
import axios from "axios";
import { computed, onMounted, ref } from "vue";

const loading = ref(false);
const companyName = ref("");
const properties = ref([]);
const bookings = ref([]);

function formatMoney(value) {
  return Number(value || 0).toLocaleString();
}

function statusClass(status) {
  if (status === "active") return "bg-red-100 text-red-800";
  if (status === "pending") return "bg-yellow-100 text-yellow-800";
  if (status === "completed") return "bg-blue-100 text-blue-800";
  return "bg-gray-100 text-gray-600";
}

const myRentals = computed(() => properties.value.length);

const availableRentals = computed(() =>
  properties.value.filter((r) => r.status === "available").length
);

const activeBookings = computed(() =>
  bookings.value.filter((b) => b.status === "active").length
);

const myRevenue = computed(() =>
  bookings.value.reduce((sum, b) => sum + (b.price || 0), 0)
);

const recentBookings = computed(() => bookings.value.slice(0, 5));

async function fetchDashboard() {
  loading.value = true;
  try {
    const { data } = await axios.get("/lessor/api/dashboard", {
      headers: { Accept: "application/json" },
    });

    companyName.value = data?.company_name || "";

    properties.value = (data?.rentals || []).map((item) => {
      const type = item.type || "";
      return {
        id: item.id,
        name: item.title,
        type,
        typeLabel: type ? type.charAt(0).toUpperCase() + type.slice(1) : "—",
        price: Number(item.price_per_day ?? 0),
        status: item.is_available ? "available" : "rented",
        bookings: item.bookings_count ?? 0,
      };
    });

    bookings.value = (data?.bookings || []).map((item) => ({
      id: item.id,
      customer: item.customer || "",
      item: item.item || "",
      start: item.start || "",
      end: item.end || "",
      price: Number(item.price ?? 0),
      status: item.status || "pending",
    }));
  } catch (error) {
    console.error("Error loading lessor dashboard:", error);
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  fetchDashboard();
});
</script>