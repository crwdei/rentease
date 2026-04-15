<template>
  <div class="bg-gradient-to-br from-purple-500 to-purple-700 min-h-screen flex justify-center items-center p-5">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-10">

      <!-- Header -->
      <div class="text-center mb-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-purple-500 to-purple-700 bg-clip-text text-transparent">
          RentEase
        </h1>
        <p class="text-gray-500 text-sm mt-2">Create a client account</p>
      </div>

      <!-- Error -->
      <div v-if="formError" class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
        {{ formError }}
      </div>

      <!-- Form -->
      <form @submit.prevent="register">
        <div class="mb-4">
          <label class="block mb-2 text-gray-700 font-medium">Full Name</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-purple-500"
          >
        </div>

        <div class="mb-4">
          <label class="block mb-2 text-gray-700 font-medium">Email Address</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-purple-500"
          >
        </div>

        <div class="mb-4">
          <label class="block mb-2 text-gray-700 font-medium">Password</label>
          <input
            v-model="form.password"
            type="password"
            required
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-purple-500"
          >
        </div>

        <div class="mb-6">
          <label class="block mb-2 text-gray-700 font-medium">Confirm Password</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            required
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-purple-500"
          >
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-gradient-to-r from-purple-500 to-purple-700 text-white py-3 rounded-lg font-semibold hover:-translate-y-0.5 transition shadow-lg"
        >
          {{ loading ? 'Creating...' : 'Create Account' }}
        </button>
      </form>

      <!-- Login link -->
      <div class="mt-4 text-center text-sm text-gray-600">
        Already have an account?
        <router-link to="/login" class="text-purple-500 font-semibold hover:underline">
          Back to Login
        </router-link>
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

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const register = async () => {
  loading.value = true
  formError.value = ''

  try {
    await axios.post('/register', form)
    router.push('/browse-rentals')
  } catch (e) {
    formError.value = e.response?.data?.message || 'Registration failed'
  } finally {
    loading.value = false
  }
}
</script>