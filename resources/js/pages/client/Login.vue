<template>
  <div class="bg-gradient-to-br from-purple-500 to-purple-700 min-h-screen flex justify-center items-center p-5">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-12">
      
      <!-- Logo -->
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-500 to-purple-700 bg-clip-text text-transparent mb-2">
          RentEase
        </h1>
        <p class="text-gray-600 text-sm">Client Portal</p>
      </div>

      <!-- Badge -->
      <div class="text-center mb-8">
        <span class="inline-block bg-gradient-to-r from-purple-500 to-purple-700 text-white px-6 py-2 rounded-full font-medium text-sm">
          👤 Customer Login
        </span>
      </div>

      <!-- Error -->
      <div v-if="formError" class="mb-6 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
        {{ formError }}
      </div>

      <!-- Form -->
      <form @submit.prevent="login">
        <div class="mb-6">
          <label class="block mb-2 text-gray-700 font-medium">Email Address</label>
          <input
            v-model="form.email"
            type="email"
            required
            placeholder="your@email.com"
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-purple-500"
          />
        </div>

        <div class="mb-6">
          <label class="block mb-2 text-gray-700 font-medium">Password</label>
          <div class="relative">
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="form.password"
              required
              placeholder="Enter your password"
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-purple-500"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 text-sm px-2 py-1"
            >
              {{ showPassword ? 'Hide' : 'Show' }}
            </button>
          </div>
        </div>

        <div class="flex justify-between items-center mb-6 text-sm">
          <label class="flex items-center gap-2 cursor-pointer text-gray-600">
            <input type="checkbox" v-model="form.remember" class="rounded">
            <span>Remember me</span>
          </label>
          <a href="#" class="text-purple-500 hover:underline">Forgot Password?</a>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-gradient-to-r from-purple-500 to-purple-700 text-white py-3 rounded-lg font-semibold hover:-translate-y-0.5 transition shadow-lg"
        >
          {{ loading ? 'Signing in...' : 'Sign In' }}
        </button>
      </form>

      <!-- Divider -->
      <div class="relative text-center my-8">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative bg-white px-4">
          <span class="text-gray-400 text-sm">OR</span>
        </div>
      </div>

      <!-- Other logins -->
      <div class="grid grid-cols-2 gap-4 mb-6">
        <a href="/admin/login" class="text-center py-2 border-2 border-gray-200 rounded-lg text-gray-600 text-sm hover:border-purple-500 hover:text-purple-500">
          Admin Login
        </a>
        <a href="/lessor/login" class="text-center py-2 border-2 border-gray-200 rounded-lg text-gray-600 text-sm hover:border-purple-500 hover:text-purple-500">
          Lessor Login
        </a>
      </div>

      <!-- Register -->
      <div class="text-center text-gray-600 text-sm">
        Don't have an account?
        <router-link to="/register" class="text-purple-500 font-semibold hover:underline">
          Sign Up
        </router-link>
      </div>

      <!-- Demo -->
      <div
        @click="fillDemo"
        class="mt-6 p-4 bg-purple-50 rounded-lg cursor-pointer hover:bg-purple-100"
      >
        <p class="text-sm text-gray-600 text-center mb-2 font-medium">Demo Credentials</p>
        <p class="text-xs text-gray-500 text-center">Email: client@rentease.com</p>
        <p class="text-xs text-gray-500 text-center">Password: client123</p>
      </div>

    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()

const loading = ref(false)
const formError = ref('')
const showPassword = ref(false)

const form = reactive({
  email: '',
  password: '',
  remember: false
})

const login = async () => {
  loading.value = true
  formError.value = ''

  try {
    await axios.post('/login', form)
    router.push('/browse-rentals')
  } catch (e) {
    formError.value = e.response?.data?.message || 'Login failed'
  } finally {
    loading.value = false
  }
}

const fillDemo = () => {
  form.email = 'client@rentease.com'
  form.password = 'client123'
}
</script>