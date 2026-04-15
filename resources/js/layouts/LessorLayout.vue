<template>
  <div class="bg-gradient-to-br from-purple-500 to-purple-700 min-h-screen p-5">
    <nav class="bg-white rounded-xl shadow-lg p-4 mb-8">
      <div class="flex justify-between items-center">
        <div class="text-2xl font-bold bg-gradient-to-r from-purple-500 to-purple-700 bg-clip-text text-transparent">
          RentEase
        </div>

        <ul class="hidden md:flex gap-8">
          <li>
            <router-link
              to="/dashboard"
              class="font-medium transition-colors"
              :class="isActive('/dashboard') ? 'text-purple-500' : 'text-gray-700 hover:text-purple-500'"
            >
              Dashboard
            </router-link>
          </li>
          <li>
            <router-link
              to="/my-properties"
              class="font-medium transition-colors"
              :class="isActive('/my-properties') ? 'text-purple-500' : 'text-gray-700 hover:text-purple-500'"
            >
              My Properties
            </router-link>
          </li>
          <li>
            <router-link
              to="/bookings"
              class="font-medium transition-colors"
              :class="isActive('/bookings') ? 'text-purple-500' : 'text-gray-700 hover:text-purple-500'"
            >
              Bookings
            </router-link>
          </li>
          <li>
            <router-link
              to="/reports"
              class="font-medium transition-colors"
              :class="isActive('/reports') ? 'text-purple-500' : 'text-gray-700 hover:text-purple-500'"
            >
              Reports
            </router-link>
          </li>
          <li>
  <router-link
    to="/settings"
    class="font-medium transition-colors"
    :class="isActive('/settings') ? 'text-purple-500' : 'text-gray-700 hover:text-purple-500'"
  >
    Settings
  </router-link>
</li>

<li>
  <router-link
    to="/profile"
    class="font-medium transition-colors"
    :class="isActive('/profile') ? 'text-purple-500' : 'text-gray-700 hover:text-purple-500'"
  >
    Profile
  </router-link>
</li>
        </ul>

        <button
          @click="logout"
          class="hidden md:block bg-gradient-to-r from-purple-500 to-purple-700 text-white px-6 py-2 rounded-lg font-medium hover:-translate-y-0.5 transition-transform"
        >
          Logout
        </button>

        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-700">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
      </div>

      <div v-if="mobileMenuOpen" class="md:hidden mt-4 pt-4 border-t border-gray-200">
        <ul class="flex flex-col gap-2">
          <li>
            <router-link
              to="/dashboard"
              class="block py-2 font-medium"
              :class="isActive('/dashboard') ? 'text-purple-500' : 'text-gray-700'"
              @click="mobileMenuOpen = false"
            >
              Dashboard
            </router-link>
          </li>
          <li>
            <router-link
              to="/my-properties"
              class="block py-2 font-medium"
              :class="isActive('/my-properties') ? 'text-purple-500' : 'text-gray-700'"
              @click="mobileMenuOpen = false"
            >
              My Properties
            </router-link>
          </li>
          <li>
            <router-link
              to="/bookings"
              class="block py-2 font-medium"
              :class="isActive('/bookings') ? 'text-purple-500' : 'text-gray-700'"
              @click="mobileMenuOpen = false"
            >
              Bookings
            </router-link>
          </li>
          <li>
            <router-link
              to="/reports"
              class="block py-2 font-medium"
              :class="isActive('/reports') ? 'text-purple-500' : 'text-gray-700'"
              @click="mobileMenuOpen = false"
            >
              Reports
            </router-link>
          </li>

          <li>
            <router-link
              to="/settings"
              class="block py-2 font-medium"
              :class="isActive('/settings') ? 'text-purple-500' : 'text-gray-700'"
              @click="mobileMenuOpen = false"
            >
              Settings
            </router-link>
          </li>

          <li>
  <router-link
    to="/profile"
    class="block py-2 font-medium"
    :class="isActive('/profile') ? 'text-purple-500' : 'text-gray-700'"
    @click="mobileMenuOpen = false"
  >
    Profile
  </router-link>
</li>
          <li>
            <button
              @click="logout"
              class="w-full text-left py-2 font-medium text-red-600"
            >
              Logout
            </button>
          </li>
        </ul>
      </div>
    </nav>

    <div class="max-w-7xl mx-auto">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { ref } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const mobileMenuOpen = ref(false);

function isActive(path) {
  return route.path === path;
}

async function logout() {
  const ok = window.confirm("Are you sure you want to logout?");
  if (!ok) return;

  try {
    await axios.post("/lessor/logout");
    window.location.href = "/lessor/login";
  } catch (error) {
    console.error("Logout failed", error);
    window.location.href = "/lessor/login";
  }
}
</script>