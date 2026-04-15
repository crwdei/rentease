<template>
  <div class="bg-gradient-to-br from-purple-500 to-purple-700 min-h-screen flex justify-center items-center p-5">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-10">
      <div class="text-center mb-6">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-500 to-purple-700 bg-clip-text text-transparent mb-2">
          RentEase
        </h1>
        <p class="text-gray-500 text-sm">Admin sign in to continue</p>
      </div>

      <div class="text-center mb-4 text-xs text-gray-500">
        Sign in as an administrator.
      </div>

      <div
        v-if="errorMessage"
        class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-3"
      >
        {{ errorMessage }}
      </div>

      <form @submit.prevent="submitLogin">
        <div class="mb-5">
          <label class="block mb-2 text-gray-700 font-medium">Email Address</label>
          <input
            v-model="form.email"
            type="email"
            required
            placeholder="you@email.com"
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-purple-500 transition-colors"
          >
        </div>

        <div class="mb-5">
          <label class="block mb-2 text-gray-700 font-medium">Password</label>
          <div class="relative">
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              required
              placeholder="Enter your password"
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-purple-500 transition-colors"
            >
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 text-xs px-2 py-1"
            >
              {{ showPassword ? "Hide" : "Show" }}
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between mb-6 text-sm">
          <label class="flex items-center gap-2 text-gray-600">
            <input v-model="form.remember" type="checkbox" class="rounded">
            <span>Remember me</span>
          </label>

          <a href="#" class="text-purple-500 hover:underline text-xs">
            Forgot password?
          </a>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-gradient-to-r from-purple-500 to-purple-700 text-white py-3 rounded-lg font-semibold hover:-translate-y-0.5 transition-transform shadow-lg disabled:opacity-60 disabled:cursor-not-allowed"
        >
          {{ loading ? "Signing In..." : "Sign In" }}
        </button>
      </form>

      <div class="mt-3 p-3 bg-purple-50 rounded-lg text-xs text-gray-500">
        <p class="font-medium text-center mb-1">Demo Account (example)</p>
        <p class="text-center">Admin: admin@rentease.com / admin123</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const loading = ref(false);
const showPassword = ref(false);
const errorMessage = ref("");

const form = reactive({
  email: "",
  password: "",
  remember: false,
});

async function submitLogin() {
  loading.value = true;
  errorMessage.value = "";

  try {
    await axios.post("/admin/login", {
      email: form.email,
      password: form.password,
      remember: form.remember,
    });

    router.push("/dashboard");
  } catch (error) {
    if (error.response?.data?.message) {
      errorMessage.value = error.response.data.message;
    } else if (error.response?.data?.errors) {
      const firstKey = Object.keys(error.response.data.errors)[0];
      errorMessage.value = error.response.data.errors[firstKey]?.[0] || "Login failed.";
    } else {
      errorMessage.value = "Login failed. Please try again.";
    }
  } finally {
    loading.value = false;
  }
}
</script>