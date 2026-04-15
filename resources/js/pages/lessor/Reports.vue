<template>
  <div>
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-2">Reports & Analytics</h1>
      <p class="text-gray-600 mb-4">View detailed reports and analytics for your business</p>

      <div class="inline-block bg-gradient-to-r from-purple-500 to-purple-700 text-white px-4 py-2 rounded-lg font-medium">
        {{ companyName }}
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
      <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="text-sm text-gray-600 mb-2">Total Revenue (This Month)</div>
        <div class="text-3xl font-bold text-gray-800">₱{{ formatMoney(stats.monthRevenue) }}</div>
      </div>

      <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="text-sm text-gray-600 mb-2">Active Bookings</div>
        <div class="text-3xl font-bold text-gray-800">{{ stats.activeBookings }}</div>
      </div>

      <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="text-sm text-gray-600 mb-2">Total Properties</div>
        <div class="text-3xl font-bold text-gray-800">{{ stats.totalProperties }}</div>
      </div>

      <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="text-sm text-gray-600 mb-2">Occupancy Rate</div>
        <div class="text-3xl font-bold text-gray-800">{{ stats.occupancyRate }}%</div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
      <h2 class="text-xl font-semibold text-gray-800 mb-6">Revenue Report</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
          <label class="block mb-2 text-gray-700 font-medium">Start Date</label>
          <input v-model="revenueFilters.startDate" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
        </div>
        <div>
          <label class="block mb-2 text-gray-700 font-medium">End Date</label>
          <input v-model="revenueFilters.endDate" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
        </div>
        <div>
          <label class="block mb-2 text-gray-700 font-medium">Property</label>
          <select v-model="revenueFilters.property" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            <option value="">All Properties</option>
          </select>
        </div>
      </div>

      <div class="flex gap-4 mb-6">
        <button @click="generateRevenue" class="bg-gradient-to-r from-purple-500 to-purple-700 text-white px-6 py-2 rounded-lg">
          Generate Report
        </button>
        <button @click="exportRevenue" class="bg-gray-600 text-white px-6 py-2 rounded-lg">
          Export to CSV
        </button>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="p-4 bg-gray-50 rounded-lg border-l-4 border-purple-500">
          <div class="text-sm text-gray-600 mb-2">Total Revenue</div>
          <div class="text-2xl font-bold text-gray-800">₱{{ formatMoney(stats.totalRevenue) }}</div>
        </div>

        <div class="p-4 bg-gray-50 rounded-lg border-l-4 border-purple-500">
          <div class="text-sm text-gray-600 mb-2">Total Bookings</div>
          <div class="text-2xl font-bold text-gray-800">{{ stats.totalBookings }}</div>
        </div>

        <div class="p-4 bg-gray-50 rounded-lg border-l-4 border-purple-500">
          <div class="text-sm text-gray-600 mb-2">Avg. Booking Value</div>
          <div class="text-2xl font-bold text-gray-800">₱{{ formatMoney(stats.avgBookingValue) }}</div>
        </div>

        <div class="p-4 bg-gray-50 rounded-lg border-l-4 border-purple-500">
          <div class="text-sm text-gray-600 mb-2">Completed Bookings</div>
          <div class="text-2xl font-bold text-gray-800">{{ stats.completedBookings }}</div>
        </div>
      </div>

      <div class="p-6 bg-gray-50 rounded-lg text-center text-gray-500">
        Revenue by property will appear here once backend data is connected.
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
      <h2 class="text-xl font-semibold text-gray-800 mb-6">Booking Report</h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
          <label class="block mb-2 text-gray-700 font-medium">Start Date</label>
          <input v-model="bookingFilters.startDate" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
        </div>
        <div>
          <label class="block mb-2 text-gray-700 font-medium">End Date</label>
          <input v-model="bookingFilters.endDate" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
        </div>
        <div>
          <label class="block mb-2 text-gray-700 font-medium">Status</label>
          <select v-model="bookingFilters.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            <option value="">All Status</option>
          </select>
        </div>
      </div>

      <div class="flex gap-4 mb-6">
        <button @click="generateBooking" class="bg-gradient-to-r from-purple-500 to-purple-700 text-white px-6 py-2 rounded-lg">
          Generate Report
        </button>
        <button @click="exportBooking" class="bg-gray-600 text-white px-6 py-2 rounded-lg">
          Export to CSV
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-gray-50">
              <th class="px-4 py-3 text-left">ID</th>
              <th class="px-4 py-3 text-left">Customer</th>
              <th class="px-4 py-3 text-left">Property</th>
              <th class="px-4 py-3 text-left">Start Date</th>
              <th class="px-4 py-3 text-left">End Date</th>
              <th class="px-4 py-3 text-left">Price</th>
              <th class="px-4 py-3 text-left">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                Booking data will appear here once backend is connected.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
      <h2 class="text-xl font-semibold text-gray-800 mb-6">Property Performance</h2>

      <div class="p-6 bg-gray-50 rounded-lg text-center text-gray-500">
        Property performance details will appear here once backend is connected.
      </div>
    </div>

    <div v-if="loading" class="mt-4 text-sm text-gray-500">Loading reports...</div>
  </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, reactive, ref } from "vue";

const loading = ref(false);
const companyName = ref("Your Company");

const stats = reactive({
  monthRevenue: 0,
  activeBookings: 0,
  totalProperties: 0,
  occupancyRate: 0,
  totalRevenue: 0,
  totalBookings: 0,
  avgBookingValue: 0,
  completedBookings: 0,
});

const revenueFilters = reactive({
  startDate: "2025-01-01",
  endDate: "2025-12-31",
  property: "",
});

const bookingFilters = reactive({
  startDate: "2025-01-01",
  endDate: "2025-12-31",
  status: "",
});

function formatMoney(value) {
  return Number(value || 0).toLocaleString();
}

async function fetchSummary() {
  loading.value = true;
  try {
    const { data } = await axios.get("/lessor/api/reports/summary", {
      headers: { Accept: "application/json" },
    });

    companyName.value = data.company_name || "Your Company";
    stats.monthRevenue = data.month_revenue ?? 0;
    stats.activeBookings = data.active_bookings ?? 0;
    stats.totalProperties = data.total_properties ?? 0;
    stats.occupancyRate = data.occupancy_rate ?? 0;
    stats.totalRevenue = data.total_revenue ?? 0;
    stats.totalBookings = data.total_bookings ?? 0;
    stats.avgBookingValue = data.avg_booking_value ?? 0;
    stats.completedBookings = data.completed_bookings ?? 0;
  } catch (error) {
    console.error("Error fetching summary:", error);
  } finally {
    loading.value = false;
  }
}

function generateRevenue() {
  window.alert(
    `Generating revenue report...\nPeriod: ${revenueFilters.startDate} to ${revenueFilters.endDate}\nProperty: ${revenueFilters.property || "All Properties"}`
  );
}

function exportRevenue() {
  const csvContent =
    "data:text/csv;charset=utf-8," +
    "Property,Revenue,Bookings\n" +
    "No Data Yet\n";

  const link = document.createElement("a");
  link.href = encodeURI(csvContent);
  link.download = "revenue_report.csv";
  link.click();
}

function generateBooking() {
  window.alert(
    `Generating booking report...\nPeriod: ${bookingFilters.startDate} to ${bookingFilters.endDate}\nStatus: ${bookingFilters.status || "All Status"}`
  );
}

function exportBooking() {
  const csvContent =
    "data:text/csv;charset=utf-8," +
    "ID,Customer,Property,Start Date,End Date,Price,Status\n" +
    "No Data Yet\n";

  const link = document.createElement("a");
  link.href = encodeURI(csvContent);
  link.download = "booking_report.csv";
  link.click();
}

onMounted(() => {
  fetchSummary();
});
</script>