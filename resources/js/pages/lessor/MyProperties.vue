<template>
  <div>
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-800 mb-2">My Properties</h1>
        <p class="text-gray-600 mb-4">Manage your rental properties</p>
        <div class="inline-block bg-gradient-to-r from-purple-500 to-purple-700 text-white px-4 py-2 rounded-lg font-medium">
          Your Company
        </div>
      </div>

      <button
        @click="openAddModal"
        class="bg-gradient-to-r from-purple-500 to-purple-700 text-white px-6 py-3 rounded-lg font-medium hover:-translate-y-0.5 transition-transform"
      >
        + Add Property
      </button>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block mb-2 text-gray-700 font-medium">Search</label>
          <input
            v-model="searchText"
            type="text"
            placeholder="Property title, description..."
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
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-gray-50">
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Property Title</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Description</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Price / Day</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredProperties.length === 0">
              <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                No properties found
              </td>
            </tr>

            <tr
              v-for="property in filteredProperties"
              :key="property.id"
              class="border-b border-gray-100 hover:bg-gray-50 transition-colors"
            >
              <td class="px-4 py-3">#{{ property.id }}</td>
              <td class="px-4 py-3">{{ property.title }}</td>
              <td class="px-4 py-3">{{ typeLabel(property.type) }}</td>
              <td class="px-4 py-3">{{ property.description }}</td>
              <td class="px-4 py-3">₱{{ formatMoney(property.price_per_day) }}</td>
              <td class="px-4 py-3">
                <span
                  class="px-3 py-1 rounded-full text-xs font-medium"
                  :class="property.status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                >
                  {{ property.status.toUpperCase() }}
                </span>
              </td>
              <td class="px-4 py-3">
                <button
                  @click="editProperty(property.id)"
                  class="text-purple-500 hover:bg-gray-100 px-3 py-1 rounded transition-colors font-medium mr-2"
                >
                  Edit
                </button>
                <button
                  @click="deleteProperty(property.id)"
                  class="text-red-500 hover:bg-gray-100 px-3 py-1 rounded transition-colors font-medium"
                >
                  Delete
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
      <div class="bg-white rounded-xl w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="p-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">{{ modalTitle }}</h2>
            <button
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600 text-3xl leading-none"
            >
              &times;
            </button>
          </div>

          <form @submit.prevent="saveProperty">
            <div class="mb-4">
              <label class="block mb-2 text-gray-700 font-medium">Property Title *</label>
              <input
                v-model="form.title"
                type="text"
                required
                placeholder="e.g., 2-Bedroom Apartment"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
              >
            </div>

            <div class="mb-4">
              <label class="block mb-2 text-gray-700 font-medium">Type *</label>
              <select
                v-model="form.type"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
              >
                <option value="">Select Type</option>
                <option value="apartment">Apartment</option>
                <option value="vehicle">Vehicle</option>
                <option value="equipment">Equipment</option>
              </select>
            </div>

            <div class="mb-4">
              <label class="block mb-2 text-gray-700 font-medium">Description *</label>
              <textarea
                v-model="form.description"
                required
                placeholder="Describe your property..."
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
              ></textarea>
            </div>

            <div class="mb-4">
              <label class="block mb-2 text-gray-700 font-medium">Price (₱ per day) *</label>
              <input
                v-model="form.price_per_day"
                type="number"
                required
                min="0"
                placeholder="1500"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
              >
            </div>

            <div class="mb-6">
              <label class="block mb-2 text-gray-700 font-medium">Status *</label>
              <select
                v-model="form.status"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
              >
                <option value="available">Available</option>
                <option value="rented">Rented</option>
              </select>
            </div>

            <div
              v-if="formError"
              class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-3"
            >
              {{ formError }}
            </div>

            <button
              type="submit"
              :disabled="saving"
              class="w-full bg-gradient-to-r from-purple-500 to-purple-700 text-white py-3 rounded-lg font-medium hover:-translate-y-0.5 transition-transform disabled:opacity-60"
            >
              {{ saving ? "Saving..." : "Save Property" }}
            </button>
          </form>
        </div>
      </div>
    </div>

    <div v-if="loading" class="mt-4 text-sm text-gray-500">Loading properties...</div>
  </div>
</template>

<script setup>
import axios from "axios";
import { computed, onMounted, onActivated, reactive, ref, watch } from "vue";
import { useRouter } from "vue-router";
const allProperties = ref([]);
const searchText = ref("");
const typeFilter = ref("");
const statusFilter = ref("");
const showModal = ref(false);
const modalTitle = ref("Add New Property");
const editingId = ref(null);
const loading = ref(false);
const saving = ref(false);
const formError = ref("");
const router = useRouter()
const form = reactive({
  title: "",
  type: "",
  description: "",
  price_per_day: "",
  status: "available",
});

const filteredProperties = computed(() => {
  const search = searchText.value.toLowerCase();

  return allProperties.value.filter((property) => {
    const matchesSearch =
      !searchText.value ||
      String(property.title || "").toLowerCase().includes(search) ||
      String(property.description || "").toLowerCase().includes(search);

    const matchesType =
      !typeFilter.value || property.type === typeFilter.value;

    const matchesStatus =
      !statusFilter.value || property.status === statusFilter.value;

    return matchesSearch && matchesType && matchesStatus;
  });
});

function formatMoney(value) {
  return Number(value || 0).toLocaleString();
}

function typeLabel(type) {
  return type ? type.charAt(0).toUpperCase() + type.slice(1) : "";
}

function resetForm() {
  form.title = "";
  form.type = "";
  form.description = "";
  form.price_per_day = "";
  form.status = "available";
}

async function fetchProperties() {
  loading.value = true;
  try {
    const response = await axios.get("/lessor/api/properties", {
      headers: { Accept: "application/json" },
    });

    // Controller returns { data: [...] }, so we need response.data.data
    const properties = response.data?.data ?? response.data;
    allProperties.value = (Array.isArray(properties) ? properties : []).map((item) => ({
      id: item.id,
      title: item.title,
      type: item.type || "",
      description: item.description || "",
      price_per_day: Number(item.price_per_day ?? 0),
      status: item.is_available ? "available" : "rented",
    }));
  } catch (error) {
    console.error("Error fetching properties:", error);
  } finally {
    loading.value = false;
  }
}

function openAddModal() {
  modalTitle.value = "Add New Property";
  editingId.value = null;
  formError.value = "";
  resetForm();
  showModal.value = true;
}

function editProperty(id) {
  const property = allProperties.value.find((p) => p.id === id);
  if (!property) return;

  modalTitle.value = "Edit Property";
  editingId.value = id;
  formError.value = "";

  form.title = property.title;
  form.type = property.type;
  form.description = property.description;
  form.price_per_day = property.price_per_day;
  form.status = property.status;

  showModal.value = true;
}

async function deleteProperty(id) {
  const ok = window.confirm("Are you sure you want to delete this property?");
  if (!ok) return;

  try {
    const { data } = await axios.delete(`/lessor/properties/${id}`, {
      headers: { Accept: "application/json" },
    });

    if (data?.success) {
      await fetchProperties();
      window.alert("Property deleted successfully!");
    } else {
      window.alert("Failed to delete property.");
    }
  } catch (error) {
    console.error("Error deleting property:", error);
    window.alert("An error occurred while deleting property.");
  }
}

async function saveProperty() {
  saving.value = true;
  formError.value = "";

  const payload = {
    title: form.title,
    type: form.type,
    description: form.description,
    price_per_day: Number(form.price_per_day || 0),
    is_available: form.status === "available",
  };

  try {
    let response;

    if (editingId.value) {
      response = await axios.put(`/lessor/properties/${editingId.value}`, payload, {
        headers: { Accept: "application/json" },
      });
    } else {
      response = await axios.post("/lessor/properties", payload, {
        headers: { Accept: "application/json" },
      });
    }

    if (response.data?.success) {
      closeModal();
      await fetchProperties();
      window.alert(editingId.value ? "Property updated successfully!" : "Property added successfully!");
    } else {
      formError.value = "Failed to save property.";
    }
  } catch (error) {
    console.error("Error saving property:", error);

    if (error.response?.data?.message) {
      formError.value = error.response.data.message;
    } else if (error.response?.data?.errors) {
      const firstKey = Object.keys(error.response.data.errors)[0];
      formError.value = error.response.data.errors[firstKey]?.[0] || "Failed to save property.";
    } else {
      formError.value = "An error occurred while saving property.";
    }
  } finally {
    saving.value = false;
  }
}

function closeModal() {
  showModal.value = false;
}

onMounted(() => {
  fetchProperties();
});

watch(() => router.currentRoute.value.path, (newPath) => {
  if (newPath === '/my-properties') fetchProperties();
});
</script>