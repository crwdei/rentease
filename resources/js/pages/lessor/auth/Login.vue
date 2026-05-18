<template>
    <div class="bg-gradient-to-br from-purple-500 to-purple-700 min-h-screen flex justify-center items-center p-5">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-10">
      <!-- Back to Login Selection -->
      <div class="mb-6">
        <a
          href="/login"
          class="inline-flex items-center text-purple-600 hover:text-purple-800 text-sm font-medium transition-colors"
        >
          <span class="mr-1">&larr;</span>
          Back to Login
        </a>
      </div>

      <div class="text-3xl font-bold bg-gradient-to-r from-purple-500 to-purple-700 bg-clip-text text-transparent text-center mb-6">
        RentEase
      </div>

      <h2 class="text-2xl font-bold text-center mb-4 text-gray-800">Lessor Login</h2>

      <div
        v-if="errorMessage"
        class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-3"
      >
        {{ errorMessage }}
      </div>

      <form @submit.prevent="submitLogin">
        <div class="mb-6">
          <label class="block mb-2 text-gray-700 font-medium">Email</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-purple-500 transition-colors"
            placeholder="lessor@rentease.com"
          >
        </div>

        <div class="mb-6">
          <label class="block mb-2 text-gray-700 font-medium">Password</label>
          <input
            v-model="form.password"
            type="password"
            required
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-purple-500 transition-colors"
            placeholder="Enter your password"
          >
        </div>

        <div class="flex items-center justify-between mb-6 text-sm">
          <label class="flex items-center gap-2 text-gray-600">
            <input v-model="form.remember" type="checkbox" class="rounded">
            <span>Remember me</span>
          </label>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-gradient-to-r from-purple-500 to-purple-700 text-white py-3 rounded-lg font-semibold hover:-translate-y-0.5 transition-transform shadow-lg disabled:opacity-60"
        >
          {{ loading ? "Logging in..." : "Login" }}
        </button>
      </form>

      <div class="mt-6 p-4 bg-purple-50 rounded-lg">
        <p class="text-sm text-gray-600 text-center mb-2 font-medium">Demo Credentials:</p>
        <p class="text-xs text-gray-500 text-center">Email: lessor@rentease.com</p>
        <p class="text-xs text-gray-500 text-center">Password: lessor123</p>
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
const errorMessage = ref("");

const form = reactive({
  email: "lessor@rentease.com",
  password: "",
  remember: false,
});

async function submitLogin() {
  loading.value = true;
  errorMessage.value = "";

  try {
    await axios.post("/lessor/login", {
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