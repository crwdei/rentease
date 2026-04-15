<template>
  <div>
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-800 mb-2">All Rentals (Read Only)</h1>
        <p class="text-gray-600">View all rental properties across all companies and lessors</p>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block mb-2 text-gray-700 font-medium">Search</label>
          <input
            v-model="searchText"
            type="text"
            placeholder="Property, description..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
        </div>

        <div>
          <label class="block mb-2 text-gray-700 font-medium">Type</label>
          <select
            v-model="typeFilter"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
            <option value="">All Types</option>
            <option value="apartment">Apartment</option>
            <option value="vehicle">Vehicle</option>
            <option value="equipment">Equipment</option>
          </select>
        </div>

        <div>
          <label class="block mb-2 text-gray-700 font-medium">Status</label>
          <select
            v-model="statusFilter"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
            <option value="">All Status</option>
            <option value="available">Available</option>
            <option value="rented">Rented</option>
          </select>
        </div>

        <div>
          <label class="block mb-2 text-gray-700 font-medium">Lessor</label>
          <input
            v-model="lessorFilter"
            type="text"
            placeholder="Lessor name..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
          >
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-gray-50">
              <th class="px-4 py-3 text-left text-xs md:text-sm font-semibold text-gray-700">ID</th>
              <th class="px-4 py-3 text-left text-xs md:text-sm font-semibold text-gray-700">Property</th>
              <th class="px-4 py-3 text-left text-xs md:text-sm font-semibold text-gray-700">Type</th>
              <th class="px-4 py-3 text-left text-xs md:text-sm font-semibold text-gray-700">Lessor</th>
              <th class="px-4 py-3 text-left text-xs md:text-sm font-semibold text-gray-700">Price / Day</th>
              <th class="px-4 py-3 text-left text-xs md:text-sm font-semibold text-gray-700">Status</th>
              <th class="px-4 py-3 text-left text-xs md:text-sm font-semibold text-gray-700">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredRentals.length === 0">
              <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                No rentals found
              </td>
            </tr>

            <tr
              v-for="rental in filteredRentals"
              :key="rental.id"
              class="border-b border-gray-100 hover:bg-gray-50 transition-colors"
            >
              <td class="px-4 py-3 text-xs md:text-sm">#{{ rental.id }}</td>
              <td class="px-4 py-3 text-xs md:text-sm">{{ rental.title }}</td>
              <td class="px-4 py-3 text-xs md:text-sm">{{ rental.type_label }}</td>
              <td class="px-4 py-3 text-xs md:text-sm">{{ rental.lessor || "-" }}</td>
              <td class="px-4 py-3 text-xs md:text-sm">₱{{ formatMoney(rental.price_per_day) }}</td>
              <td class="px-4 py-3 text-xs md:text-sm">
                <span
                  class="px-3 py-1 rounded-full text-[10px] md:text-xs font-medium"
                  :class="rental.status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                >
                  {{ rental.status.toUpperCase() }}
                </span>
              </td>
              <td class="px-4 py-3 text-xs md:text-sm">
                <button
                  @click="viewRental(rental.id)"
                  class="text-purple-500 hover:bg-gray-100 px-3 py-1 rounded transition-colors font-medium"
                >
                  View
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
      <div class="bg-white rounded-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="p-5 md:p-6">
          <div class="flex justify-between items-center mb-4 md:mb-6">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Rental Details</h2>
            <button
              @click="closeDetailsModal"
              class="text-gray-400 hover:text-gray-600 text-2xl md:text-3xl"
            >
              &times;
            </button>
          </div>

          <div v-if="viewingRental" class="space-y-3 md:space-y-4 text-sm md:text-base">
            <div class="flex justify-between border-b border-gray-100 pb-2">
              <span class="font-semibold text-gray-700">ID:</span>
              <span class="text-gray-600">#{{ viewingRental.id }}</span>
            </div>

            <div class="flex justify-between border-b border-gray-100 pb-2">
              <span class="font-semibold text-gray-700">Property:</span>
              <span class="text-gray-600 text-right">{{ viewingRental.title }}</span>
            </div>

            <div class="flex justify-between border-b border-gray-100 pb-2">
              <span class="font-semibold text-gray-700">Type:</span>
              <span class="text-gray-600">{{ viewingRental.type_label }}</span>
            </div>

            <div class="flex justify-between border-b border-gray-100 pb-2">
              <span class="font-semibold text-gray-700">Lessor:</span>
              <span class="text-gray-600 text-right">{{ viewingRental.lessor || "-" }}</span>
            </div>

            <div class="flex justify-between border-b border-gray-100 pb-2">
              <span class="font-semibold text-gray-700">Price / Day:</span>
              <span class="text-gray-600">₱{{ formatMoney(viewingRental.price_per_day) }}</span>
            </div>

            <div class="flex justify-between border-b border-gray-100 pb-2">
              <span class="font-semibold text-gray-700">Status:</span>
              <span
                class="px-3 py-1 rounded-full text-[10px] md:text-xs font-medium"
                :class="viewingRental.status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
              >
                {{ viewingRental.status.toUpperCase() }}
              </span>
            </div>

            <div>
              <span class="font-semibold text-gray-700 block mb-1">Description:</span>
              <p class="text-gray-600 whitespace-pre-line">{{ viewingRental.description || "—" }}</p>
            </div>
          </div>

          <div class="mt-5 md:mt-6">
            <button
              @click="closeDetailsModal"
              class="w-full bg-gray-200 text-gray-700 py-2.5 md:py-3 rounded-lg font-medium hover:bg-gray-300 transition-colors"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="loading" class="mt-4 text-sm text-gray-500">Loading rentals...</div>
  </div>
</template>

<script setup>
import axios from "axios";
import { computed, onMounted, ref } from "vue";

const loading = ref(false);
const allRentals = ref([]);
const searchText = ref("");
const typeFilter = ref("");
const statusFilter = ref("");
const lessorFilter = ref("");
const showDetailsModal = ref(false);
const viewingRental = ref(null);

function formatMoney(value) {
  return Number(value || 0).toLocaleString();
}

const filteredRentals = computed(() => {
  const search = searchText.value.toLowerCase();
  const lessor = lessorFilter.value.toLowerCase();

  return allRentals.value.filter((rental) => {
    const matchesSearch =
      !searchText.value ||
      String(rental.title || "").toLowerCase().includes(search) ||
      String(rental.description || "").toLowerCase().includes(search);

    const matchesType =
      !typeFilter.value || rental.type === typeFilter.value;

    const matchesStatus =
      !statusFilter.value || rental.status === statusFilter.value;

    const matchesLessor =
      !lessorFilter.value ||
      String(rental.lessor || "").toLowerCase().includes(lessor);

    return matchesSearch && matchesType && matchesStatus && matchesLessor;
  });
});

async function fetchRentals() {
  loading.value = true;
  try {
    const { data } = await axios.get("/admin/api/rentals", {
      headers: { Accept: "application/json" },
    });

    allRentals.value = (Array.isArray(data) ? data : []).map((item) => {
      const type = item.type || "";
      const typeLabel = type ? type.charAt(0).toUpperCase() + type.slice(1) : "—";

      return {
        id: item.id,
        title: item.title,
        type,
        type_label: typeLabel,
        description: item.description || "",
        price_per_day: Number(item.price_per_day ?? 0),
        status: item.is_available ? "available" : "rented",
        lessor: item.lessor_name || "",
      };
    });
  } catch (error) {
    console.error("Error fetching rentals:", error);
  } finally {
    loading.value = false;
  }
}

function viewRental(id) {
  viewingRental.value = allRentals.value.find((r) => r.id === id) || null;
  showDetailsModal.value = !!viewingRental.value;
}

function closeDetailsModal() {
  showDetailsModal.value = false;
  viewingRental.value = null;
}

onMounted(() => {
  fetchRentals();
});
</script>