<template>
  <div>
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
      <h1 class="text-3xl font-bold text-gray-800 mb-2">Manage Bookings</h1>
      <p class="text-gray-600">View and manage all rental bookings across the system</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block mb-2 text-gray-700 font-medium">Search</label>
          <input
            v-model="searchText"
            type="text"
            placeholder="Customer, email, property..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
        </div>
        <div>
          <label class="block mb-2 text-gray-700 font-medium">Status</label>
          <select
            v-model="statusFilter"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="active">Active</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-gray-50">
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Customer</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Property</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Start Date</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">End Date</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredBookings.length === 0">
              <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                No bookings found
              </td>
            </tr>

            <tr
              v-for="booking in filteredBookings"
              :key="booking.id"
              class="border-b border-gray-100 hover:bg-gray-50 transition-colors"
            >
              <td class="px-4 py-3">#{{ booking.id }}</td>
              <td class="px-4 py-3">{{ booking.customer }}</td>
              <td class="px-4 py-3">{{ booking.email }}</td>
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
              <td class="px-4 py-3">
                <button
                  @click="viewDetails(booking.id)"
                  class="text-purple-500 hover:bg-gray-100 px-3 py-1 rounded transition-colors font-medium mr-2"
                >
                  View
                </button>

                <button
                  v-if="booking.status === 'pending'"
                  @click="openConfirmModal(booking.id)"
                  class="text-green-600 hover:bg-gray-100 px-3 py-1 rounded transition-colors font-medium mr-2"
                >
                  Confirm
                </button>

                <button
                  v-if="booking.status !== 'cancelled' && booking.status !== 'completed'"
                  @click="openCancelModal(booking.id)"
                  class="text-red-500 hover:bg-gray-100 px-3 py-1 rounded transition-colors font-medium"
                >
                  Cancel
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div
      v-if="showDetailsModal"
      class="fixed inset-0 bg-black/30 z-50 flex items-center justify-center p-4"
    >
      <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Booking Details</h2>
            <button
              @click="closeDetailsModal"
              class="text-gray-400 hover:text-gray-600 text-3xl"
            >
              &times;
            </button>
          </div>

          <div v-if="viewingBooking">
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Booking ID:</span>
              <span class="text-gray-600">#{{ viewingBooking.id }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Customer Name:</span>
              <span class="text-gray-600">{{ viewingBooking.customer }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Email:</span>
              <span class="text-gray-600">{{ viewingBooking.email }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Phone:</span>
              <span class="text-gray-600">{{ viewingBooking.phone }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Property:</span>
              <span class="text-gray-600">{{ viewingBooking.item }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Start Date:</span>
              <span class="text-gray-600">{{ viewingBooking.start }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">End Date:</span>
              <span class="text-gray-600">{{ viewingBooking.end }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Total Price:</span>
              <span class="text-gray-600">₱{{ formatMoney(viewingBooking.price) }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Valid ID:</span>
              <span class="text-gray-600">{{ viewingBooking.validId }}</span>
            </div>
            <div class="flex justify-between py-3">
              <span class="font-semibold text-gray-700">Status:</span>
              <span
                class="px-3 py-1 rounded-full text-xs font-medium"
                :class="statusClass(viewingBooking.status)"
              >
                {{ viewingBooking.status.toUpperCase() }}
              </span>
            </div>
          </div>

          <div class="mt-6">
            <button
              @click="closeDetailsModal"
              class="w-full bg-gray-200 text-gray-700 py-3 rounded-lg font-medium hover:bg-gray-300 transition-colors"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <div
      v-if="showConfirmModal"
      class="fixed inset-0 bg-black/30 z-50 flex items-center justify-center p-4"
    >
      <div class="bg-white rounded-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-800">Confirm Booking</h2>
          <button
            @click="closeConfirmModal"
            class="text-gray-400 hover:text-gray-600 text-3xl"
          >
            &times;
          </button>
        </div>
        <p class="mb-6 text-gray-600">Are you sure you want to confirm this booking?</p>
        <div class="flex gap-4">
          <button
            @click="closeConfirmModal"
            class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg font-medium hover:bg-gray-300 transition-colors"
          >
            Cancel
          </button>
          <button
            @click="confirmBooking"
            class="flex-1 bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700 transition-colors"
          >
            Confirm
          </button>
        </div>
      </div>
    </div>

    <div
      v-if="showCancelModal"
      class="fixed inset-0 bg-black/30 z-50 flex items-center justify-center p-4"
    >
      <div class="bg-white rounded-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-800">Cancel Booking</h2>
          <button
            @click="closeCancelModal"
            class="text-gray-400 hover:text-gray-600 text-3xl"
          >
            &times;
          </button>
        </div>
        <p class="mb-6 text-gray-600">Are you sure you want to cancel this booking? This action cannot be undone.</p>
        <div class="flex gap-4">
          <button
            @click="closeCancelModal"
            class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg font-medium hover:bg-gray-300 transition-colors"
          >
            No, Keep It
          </button>
          <button
            @click="cancelBooking"
            class="flex-1 bg-red-600 text-white py-3 rounded-lg font-medium hover:bg-red-700 transition-colors"
          >
            Yes, Cancel
          </button>
        </div>
      </div>
    </div>

    <div v-if="loading" class="mt-4 text-sm text-gray-500">Loading bookings...</div>
  </div>
</template>

<script setup>
import axios from "axios";
import { computed, onMounted, ref } from "vue";

const allBookings = ref([]);
const searchText = ref("");
const statusFilter = ref("");
const showDetailsModal = ref(false);
const showConfirmModal = ref(false);
const showCancelModal = ref(false);
const viewingBooking = ref(null);
const selectedBookingId = ref(null);
const loading = ref(false);

const filteredBookings = computed(() => {
  const search = searchText.value.toLowerCase();

  return allBookings.value.filter((booking) => {
    const matchesSearch =
      !searchText.value ||
      String(booking.customer || "").toLowerCase().includes(search) ||
      String(booking.email || "").toLowerCase().includes(search) ||
      String(booking.item || "").toLowerCase().includes(search);

    const matchesStatus =
      !statusFilter.value || booking.status === statusFilter.value;

    return matchesSearch && matchesStatus;
  });
});

function formatMoney(value) {
  return Number(value || 0).toLocaleString();
}

function statusClass(status) {
  if (status === "active") return "bg-red-100 text-red-800";
  if (status === "pending") return "bg-yellow-100 text-yellow-800";
  if (status === "completed") return "bg-blue-100 text-blue-800";
  if (status === "cancelled") return "bg-gray-100 text-gray-600";
  return "bg-gray-100 text-gray-600";
}

async function fetchBookings() {
  loading.value = true;
  try {
    const { data } = await axios.get("/admin/api/bookings", {
      headers: { Accept: "application/json" },
    });

    allBookings.value = (Array.isArray(data) ? data : []).map((item) => ({
      id: item.id,
      customer: item.customer_name || item.client_name || "",
      email: item.customer_email || "",
      phone: item.customer_phone || "",
      item: item.rental_title || "",
      start: item.start_date,
      end: item.end_date,
      price: Number(item.total_price ?? 0),
      status: item.status || "pending",
      validId: "",
    }));
  } catch (error) {
    console.error("Error fetching bookings:", error);
  } finally {
    loading.value = false;
  }
}

function viewDetails(id) {
  viewingBooking.value = allBookings.value.find((b) => b.id === id) || null;
  showDetailsModal.value = !!viewingBooking.value;
}

function closeDetailsModal() {
  showDetailsModal.value = false;
  viewingBooking.value = null;
}

function openConfirmModal(id) {
  selectedBookingId.value = id;
  showConfirmModal.value = true;
}

function closeConfirmModal() {
  showConfirmModal.value = false;
  selectedBookingId.value = null;
}

async function confirmBooking() {
  if (!selectedBookingId.value) return;

  try {
    const { data } = await axios.patch(`/admin/bookings/${selectedBookingId.value}/status`, {
      status: "active",
    });

    if (data?.success) {
      await fetchBookings();
      closeConfirmModal();
      window.alert("Booking confirmed successfully!");
    } else {
      window.alert("Failed to confirm booking.");
    }
  } catch (error) {
    console.error("Error confirming booking:", error);
    window.alert("An error occurred while confirming booking.");
  }
}

function openCancelModal(id) {
  selectedBookingId.value = id;
  showCancelModal.value = true;
}

function closeCancelModal() {
  showCancelModal.value = false;
  selectedBookingId.value = null;
}

async function cancelBooking() {
  if (!selectedBookingId.value) return;

  try {
    const { data } = await axios.patch(`/admin/bookings/${selectedBookingId.value}/status`, {
      status: "cancelled",
    });

    if (data?.success) {
      await fetchBookings();
      closeCancelModal();
      window.alert("Booking cancelled successfully!");
    } else {
      window.alert("Failed to cancel booking.");
    }
  } catch (error) {
    console.error("Error cancelling booking:", error);
    window.alert("An error occurred while cancelling booking.");
  }
}

onMounted(() => {
  fetchBookings();
});
</script>