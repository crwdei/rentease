<template>
  <div class="bg-gradient-to-br from-purple-500 to-purple-700 min-h-screen p-5">
    
    <!-- Navbar -->
    <nav class="bg-white rounded-xl shadow-lg p-4 mb-8">
      <div class="flex justify-between items-center">

        <!-- Logo -->
        <div class="text-2xl font-bold bg-gradient-to-r from-purple-500 to-purple-700 bg-clip-text text-transparent">
          RentEase
        </div>

        <!-- Desktop Nav -->
        <ul class="hidden md:flex gap-8">
          <li>
            <router-link
              to="/browse-rentals"
              class="font-medium transition-colors"
              :class="isActive('/browse-rentals') ? 'text-purple-500' : 'text-gray-700 hover:text-purple-500'"
            >
              Browse Rentals
            </router-link>
          </li>
          <li>
            <router-link
              to="/my-bookings"
              class="font-medium transition-colors"
              :class="isActive('/my-bookings') ? 'text-purple-500' : 'text-gray-700 hover:text-purple-500'"
            >
              My Bookings
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

        <!-- Logout -->
        <button
          @click="logout"
          class="hidden md:block bg-gradient-to-r from-purple-500 to-purple-700 text-white px-6 py-2 rounded-lg font-medium hover:-translate-y-0.5 transition"
        >
          Logout
        </button>

        <!-- Mobile Menu Button -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-700">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>

      <!-- Mobile Menu -->
      <div v-if="mobileMenuOpen" class="md:hidden mt-4 pt-4 border-t border-gray-200">
        <ul class="flex flex-col gap-2">
          <li>
            <router-link
              to="/browse-rentals"
              class="block py-2 font-medium"
              :class="isActive('/browse-rentals') ? 'text-purple-500' : 'text-gray-700'"
            >
              Browse Rentals
            </router-link>
          </li>
          <li>
            <router-link
              to="/my-bookings"
              class="block py-2 font-medium"
              :class="isActive('/my-bookings') ? 'text-purple-500' : 'text-gray-700'"
            >
              My Bookings
            </router-link>
          </li>
          
          <li>
            <router-link
              to="/settings"
              class="block py-2 font-medium"
              :class="isActive('/settings') ? 'text-purple-500' : 'text-gray-700'"
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
            <button @click="logout" class="w-full text-left py-2 font-medium text-red-600">
              Logout
            </button>
          </li>
        </ul>
      </div>
    </nav>

    <!-- Content -->
    <div class="max-w-7xl mx-auto">
      <router-view />
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

const mobileMenuOpen = ref(false)

const isActive = (path) => route.path === path

const logout = async () => {
  if (!confirm('Are you sure you want to logout?')) return

  await axios.post('/logout')
  router.push('/login')
}
</script>