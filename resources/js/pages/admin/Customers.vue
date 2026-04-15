<template>
  <div>
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-2">Customer Management</h1>
      <p class="text-gray-600">View and manage all registered customers</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
      <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="text-3xl font-bold text-gray-800 mb-1">{{ totalCustomers }}</div>
        <div class="text-sm text-gray-600">Total Customers</div>
      </div>

      <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="text-3xl font-bold text-gray-800 mb-1">{{ activeCustomers }}</div>
        <div class="text-sm text-gray-600">Active Customers</div>
      </div>

      <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="text-3xl font-bold text-gray-800 mb-1">{{ totalBookingsCount }}</div>
        <div class="text-sm text-gray-600">Total Bookings</div>
      </div>

      <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="text-3xl font-bold text-gray-800 mb-1">₱{{ formatMoney(totalRevenue) }}</div>
        <div class="text-sm text-gray-600">Total Revenue</div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
      <div>
        <label class="block mb-2 text-gray-700 font-medium">Search</label>
        <input
          v-model="searchText"
          type="text"
          placeholder="Name, email, phone..."
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
        >
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-gray-50">
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Name</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Phone</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Bookings</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total Spent</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredCustomers.length === 0">
              <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                No customers found
              </td>
            </tr>

            <tr
              v-for="customer in filteredCustomers"
              :key="customer.id"
              class="border-b border-gray-100 hover:bg-gray-50 transition-colors"
            >
              <td class="px-4 py-3">#{{ customer.id }}</td>
              <td class="px-4 py-3">{{ customer.name }}</td>
              <td class="px-4 py-3">{{ customer.email }}</td>
              <td class="px-4 py-3">{{ customer.phone }}</td>
              <td class="px-4 py-3">{{ customer.totalBookings }}</td>
              <td class="px-4 py-3">₱{{ formatMoney(customer.totalSpent) }}</td>
              <td class="px-4 py-3">
                <button
                  @click="viewCustomer(customer.id)"
                  class="text-purple-500 hover:bg-gray-100 px-3 py-1 rounded transition-colors font-medium"
                >
                  View Details
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/30 z-50 flex items-center justify-center p-4"
    >
      <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Customer Details</h2>
            <button
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600 text-3xl"
            >
              &times;
            </button>
          </div>

          <div v-if="viewingCustomer">
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Customer ID:</span>
              <span class="text-gray-600">#{{ viewingCustomer.id }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Full Name:</span>
              <span class="text-gray-600">{{ viewingCustomer.name }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Email:</span>
              <span class="text-gray-600">{{ viewingCustomer.email }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Phone:</span>
              <span class="text-gray-600">{{ viewingCustomer.phone }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Join Date:</span>
              <span class="text-gray-600">{{ viewingCustomer.joinDate }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Total Bookings:</span>
              <span class="text-gray-600">{{ viewingCustomer.totalBookings }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Total Spent:</span>
              <span class="text-gray-600">₱{{ formatMoney(viewingCustomer.totalSpent) }}</span>
            </div>

            <div v-if="customerBookings.length > 0" class="mt-6">
              <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Bookings</h3>
              <div
                v-for="(booking, index) in customerBookings"
                :key="index"
                class="bg-gray-50 rounded-lg p-4 mb-3"
              >
                <div class="flex justify-between items-start mb-2">
                  <span class="font-semibold text-gray-800">{{ booking.item }}</span>
                  <span class="text-purple-600 font-semibold">₱{{ formatMoney(booking.price) }}</span>
                </div>
                <div class="text-sm text-gray-600">{{ booking.date }}</div>
              </div>
            </div>
          </div>

          <div class="mt-6">
            <button
              @click="closeModal"
              class="w-full bg-gray-200 text-gray-700 py-3 rounded-lg font-medium hover:bg-gray-300 transition-colors"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="loading" class="mt-4 text-sm text-gray-500">Loading customers...</div>
  </div>
</template>

<script setup>
import axios from "axios";
import { computed, onMounted, ref } from "vue";

const customers = ref([]);
const searchText = ref("");
const showModal = ref(false);
const viewingCustomer = ref(null);
const customerBookings = ref([]);
const loading = ref(false);

const filteredCustomers = computed(() => {
  if (!searchText.value) return customers.value;

  const search = searchText.value.toLowerCase();

  return customers.value.filter((customer) => {
    return (
      String(customer.name || "").toLowerCase().includes(search) ||
      String(customer.email || "").toLowerCase().includes(search) ||
      String(customer.phone || "").includes(searchText.value)
    );
  });
});

const totalCustomers = computed(() => customers.value.length);

const activeCustomers = computed(() =>
  customers.value.filter((c) => c.totalBookings > 0).length
);

const totalBookingsCount = computed(() =>
  customers.value.reduce((sum, c) => sum + (c.totalBookings || 0), 0)
);

const totalRevenue = computed(() =>
  customers.value.reduce((sum, c) => sum + (c.totalSpent || 0), 0)
);

function formatMoney(value) {
  return Number(value || 0).toLocaleString();
}

async function fetchCustomers() {
  loading.value = true;
  try {
    const { data } = await axios.get("/admin/api/customers", {
      headers: { Accept: "application/json" },
    });

    customers.value = (Array.isArray(data) ? data : []).map((item) => ({
      id: item.id,
      name: item.name,
      email: item.email,
      phone: item.phone || "",
      joinDate: item.join_date || "",
      totalBookings: item.total_bookings ?? 0,
      totalSpent: Number(item.total_spent ?? 0),
    }));
  } catch (error) {
    console.error("Error fetching customers:", error);
  } finally {
    loading.value = false;
  }
}

async function viewCustomer(id) {
  viewingCustomer.value = customers.value.find((c) => c.id === id) || null;
  customerBookings.value = [];
  if (!viewingCustomer.value) return;

  try {
    const { data } = await axios.get(`/admin/api/customers/${id}/bookings`, {
      headers: { Accept: "application/json" },
    });

    customerBookings.value = (Array.isArray(data) ? data : []).map((b) => ({
      item: b.item,
      date: b.date,
      price: Number(b.price ?? 0),
    }));
  } catch (error) {
    console.error("Error fetching customer bookings:", error);
  }

  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  viewingCustomer.value = null;
  customerBookings.value = [];
}

onMounted(() => {
  fetchCustomers();
});
</script>