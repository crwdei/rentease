<template>
  <div>
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Manage Companies/Lessors</h1>
        <p class="text-gray-600">Manage all rental companies and property owners</p>
      </div>
      <button
        @click="openAddModal"
        class="bg-gradient-to-r from-purple-500 to-purple-700 text-white px-6 py-3 rounded-lg font-medium hover:-translate-y-0.5 transition-transform"
      >
        + Add Company
      </button>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block mb-2 text-gray-700 font-medium">Search</label>
          <input
            v-model="searchText"
            type="text"
            placeholder="Company name, owner, email..."
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
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
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
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Company Name</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Owner</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Phone</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Properties</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
              <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredCompanies.length === 0">
              <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                No companies found
              </td>
            </tr>

            <tr
              v-for="company in filteredCompanies"
              :key="company.id"
              class="border-b border-gray-100 hover:bg-gray-50 transition-colors"
            >
              <td class="px-4 py-3">#{{ company.id }}</td>
              <td class="px-4 py-3">{{ company.name }}</td>
              <td class="px-4 py-3">{{ company.owner }}</td>
              <td class="px-4 py-3">{{ company.email }}</td>
              <td class="px-4 py-3">{{ company.phone }}</td>
              <td class="px-4 py-3">{{ company.properties }}</td>
              <td class="px-4 py-3">
                <span
                  class="px-3 py-1 rounded-full text-xs font-medium"
                  :class="company.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                >
                  {{ company.status?.toUpperCase() }}
                </span>
              </td>
              <td class="px-4 py-3">
                <button
                  @click="viewCompany(company.id)"
                  class="text-purple-500 hover:bg-gray-100 px-3 py-1 rounded transition-colors font-medium mr-2"
                >
                  View
                </button>
                <button
                  @click="editCompany(company.id)"
                  class="text-purple-500 hover:bg-gray-100 px-3 py-1 rounded transition-colors font-medium mr-2"
                >
                  Edit
                </button>
                <button
                  @click="deleteCompany(company.id)"
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
      class="fixed inset-0 bg-black/20 z-50 flex items-center justify-center px-2 sm:px-4"
    >
      <div class="bg-white rounded-2xl w-full max-w-lg sm:max-w-xl lg:max-w-2xl max-h-[80vh] overflow-y-auto shadow-xl">
        <div class="p-4 sm:p-6 space-y-4">
          <div class="flex items-start justify-between gap-3">
            <div>
              <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ modalTitle }}</h2>
              <p class="text-xs text-gray-500 mt-1">
                Add or update a company and its lessor login credentials.
              </p>
            </div>
            <button
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600 text-2xl leading-none"
            >
              &times;
            </button>
          </div>

          <form @submit.prevent="saveCompany" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="md:col-span-2">
                <label class="block mb-1 text-sm font-medium text-gray-700">Company Name *</label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  placeholder="ABC Rentals Inc."
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                >
              </div>

              <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Owner Name *</label>
                <input
                  v-model="form.owner"
                  type="text"
                  required
                  placeholder="Juan Dela Cruz"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                >
              </div>

              <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Company Email *</label>
                <input
                  v-model="form.email"
                  type="email"
                  required
                  placeholder="juan@abc.com"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                >
              </div>

              <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Phone *</label>
                <input
                  v-model="form.phone"
                  type="tel"
                  required
                  placeholder="+63 912 345 6789"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                >
              </div>

              <div class="md:col-span-2">
                <label class="block mb-1 text-sm font-medium text-gray-700">Address *</label>
                <textarea
                  v-model="form.address"
                  required
                  rows="2"
                  placeholder="Complete address..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                ></textarea>
              </div>

              <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Status *</label>
                <select
                  v-model="form.status"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                >
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
              </div>
            </div>

            <div class="pt-3 border-t border-gray-200 space-y-3">
              <div>
                <h3 class="text-sm font-semibold text-gray-800">Lessor Login</h3>
                <p class="text-[11px] text-gray-500">
                  These credentials are used to sign in to the lessor portal.
                </p>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                  <label class="block mb-1 text-sm font-medium text-gray-700">Login Email *</label>
                  <input
                    v-model="form.login_email"
                    type="email"
                    required
                    placeholder="lessor-login@example.com"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                  >
                </div>

                <div class="md:col-span-2" v-if="!editingId">
                  <label class="block mb-1 text-sm font-medium text-gray-700">Password *</label>
                  <input
                    v-model="form.password"
                    type="password"
                    required
                    minlength="6"
                    placeholder="Initial password for lessor"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                  >
                  <small class="block mt-1 text-[11px] text-gray-500">
                    Share this password with the lessor; they can change it after logging in.
                  </small>
                </div>

                <div class="md:col-span-2" v-else>
                  <label class="block mb-1 text-sm font-medium text-gray-700">New Password (optional)</label>
                  <input
                    v-model="form.password"
                    type="password"
                    minlength="6"
                    placeholder="Leave blank to keep current password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                  >
                  <small class="block mt-1 text-[11px] text-gray-500">
                    Leave blank if you don't want to change the lessor’s password.
                  </small>
                </div>
              </div>
            </div>

            <div v-if="formError" class="text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
              {{ formError }}
            </div>

            <div class="pt-2 flex flex-col sm:flex-row gap-3">
              <button
                type="button"
                @click="closeModal"
                class="w-full sm:w-1/3 bg-gray-100 text-gray-700 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="w-full sm:flex-1 bg-gradient-to-r from-purple-500 to-purple-700 text-white py-2.5 rounded-lg text-sm font-medium hover:-translate-y-0.5 transition-transform disabled:opacity-60"
              >
                {{ saving ? "Saving..." : "Save Company" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div
      v-if="showViewModal"
      class="fixed inset-0 bg-black/30 z-50 flex items-center justify-center p-4"
    >
      <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Company Details</h2>
            <button
              @click="closeViewModal"
              class="text-gray-400 hover:text-gray-600 text-3xl"
            >
              &times;
            </button>
          </div>

          <div v-if="viewingCompany">
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Company ID:</span>
              <span class="text-gray-600">#{{ viewingCompany.id }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Company Name:</span>
              <span class="text-gray-600">{{ viewingCompany.name }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Owner:</span>
              <span class="text-gray-600">{{ viewingCompany.owner }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Email:</span>
              <span class="text-gray-600">{{ viewingCompany.email }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Phone:</span>
              <span class="text-gray-600">{{ viewingCompany.phone }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Address:</span>
              <span class="text-gray-600">{{ viewingCompany.address }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="font-semibold text-gray-700">Total Properties:</span>
              <span class="text-gray-600">{{ viewingCompany.properties }}</span>
            </div>
            <div class="flex justify-between py-3">
              <span class="font-semibold text-gray-700">Status:</span>
              <span
                class="px-3 py-1 rounded-full text-xs font-medium"
                :class="viewingCompany.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
              >
                {{ viewingCompany.status?.toUpperCase() }}
              </span>
            </div>
          </div>

          <div class="mt-6">
            <button
              @click="closeViewModal"
              class="w-full bg-gray-200 text-gray-700 py-3 rounded-lg font-medium hover:bg-gray-300 transition-colors"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="loading" class="mt-4 text-sm text-gray-500">Loading companies...</div>
  </div>
</template>

<script setup>
import axios from "axios";
import { computed, onMounted, reactive, ref } from "vue";

const loading = ref(false);
const saving = ref(false);
const searchText = ref("");
const statusFilter = ref("");
const allCompanies = ref([]);
const showModal = ref(false);
const showViewModal = ref(false);
const modalTitle = ref("Add New Company");
const editingId = ref(null);
const viewingCompany = ref(null);
const formError = ref("");

const form = reactive({
  name: "",
  owner: "",
  email: "",
  phone: "",
  address: "",
  status: "active",
  login_email: "",
  password: "",
});

const filteredCompanies = computed(() => {
  return allCompanies.value.filter((company) => {
    const search = searchText.value.toLowerCase();

    const matchesSearch =
      !searchText.value ||
      String(company.name || "").toLowerCase().includes(search) ||
      String(company.owner || "").toLowerCase().includes(search) ||
      String(company.email || "").toLowerCase().includes(search);

    const matchesStatus =
      !statusFilter.value || company.status === statusFilter.value;

    return matchesSearch && matchesStatus;
  });
});

function resetForm() {
  form.name = "";
  form.owner = "";
  form.email = "";
  form.phone = "";
  form.address = "";
  form.status = "active";
  form.login_email = "";
  form.password = "";
}

async function fetchCompanies() {
  loading.value = true;
  try {
   const { data } = await axios.get("/admin/api/companies", {
      headers: { Accept: "application/json" },
    });
    allCompanies.value = Array.isArray(data) ? data : [];
  } catch (error) {
    console.error("Error fetching companies:", error);
  } finally {
    loading.value = false;
  }
}

function openAddModal() {
  modalTitle.value = "Add New Company";
  editingId.value = null;
  formError.value = "";
  resetForm();
  showModal.value = true;
}

function editCompany(id) {
  const company = allCompanies.value.find((c) => c.id === id);
  if (!company) return;

  modalTitle.value = "Edit Company";
  editingId.value = id;
  formError.value = "";

  form.name = company.name || "";
  form.owner = company.owner || "";
  form.email = company.email || "";
  form.phone = company.phone || "";
  form.address = company.address || "";
  form.status = company.status || "active";
  form.login_email = company.login_email || company.email || "";
  form.password = "";

  showModal.value = true;
}

function viewCompany(id) {
  viewingCompany.value = allCompanies.value.find((c) => c.id === id) || null;
  showViewModal.value = true;
}

async function deleteCompany(id) {
  const ok = window.confirm("Are you sure you want to delete this company? This action cannot be undone.");
  if (!ok) return;

  try {
    const { data } = await axios.delete(`/admin/companies/${id}`, {
      headers: { Accept: "application/json" },
    });

    if (data?.success) {
      allCompanies.value = allCompanies.value.filter((c) => c.id !== id);
      window.alert("Company deleted successfully!");
    } else {
      window.alert("Failed to delete company.");
    }
  } catch (error) {
    console.error("Error deleting company:", error);
    window.alert("An error occurred while deleting company.");
  }
}

async function saveCompany() {
  saving.value = true;
  formError.value = "";

  const payload = {
    name: form.name,
    owner: form.owner,
    email: form.email,
    phone: form.phone,
    address: form.address,
    status: form.status,
    login_email: form.login_email,
  };

  if (!editingId.value || form.password) {
    payload.password = form.password;
  }

  try {
    let response;

    if (editingId.value) {
      response = await axios.put(`/admin/companies/${editingId.value}`, payload, {
        headers: { Accept: "application/json" },
      });
    } else {
      response = await axios.post("/admin/companies", payload, {
        headers: { Accept: "application/json" },
      });
    }

    const result = response.data;

    if (result?.success && result?.company) {
      const mapped = {
        id: result.company.id,
        name: result.company.name,
        owner: result.company.owner_name,
        email: result.company.email,
        phone: result.company.phone,
        address: result.company.address,
        status: result.company.status,
        properties: result.company.rentals_count ?? 0,
        login_email: form.login_email,
      };

      if (editingId.value) {
        const index = allCompanies.value.findIndex((c) => c.id === editingId.value);
        if (index !== -1) allCompanies.value[index] = mapped;
        window.alert("Company updated successfully!");
      } else {
        allCompanies.value.push(mapped);
        window.alert("Company added successfully!");
      }

      closeModal();
    } else {
      formError.value = "Failed to save company.";
    }
  } catch (error) {
    console.error("Error saving company:", error);

    if (error.response?.data?.message) {
      formError.value = error.response.data.message;
    } else if (error.response?.data?.errors) {
      const firstKey = Object.keys(error.response.data.errors)[0];
      formError.value = error.response.data.errors[firstKey]?.[0] || "Failed to save company.";
    } else {
      formError.value = "An error occurred while saving company.";
    }
  } finally {
    saving.value = false;
  }
}

function closeModal() {
  showModal.value = false;
}

function closeViewModal() {
  showViewModal.value = false;
  viewingCompany.value = null;
}

onMounted(() => {
  fetchCompanies();
});
</script>