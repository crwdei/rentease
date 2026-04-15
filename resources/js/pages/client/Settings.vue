<template>
  <div class="max-w-lg mx-auto bg-white rounded-xl shadow-lg p-8">
    <h2 class="text-2xl font-bold text-purple-700 mb-6">Account Settings</h2>

    <div v-if="successMsg" class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg">{{ successMsg }}</div>
    <div v-if="errorMsg" class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">{{ errorMsg }}</div>

    <form @submit.prevent="save" class="flex flex-col gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
        <input v-model="form.name" type="text" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input v-model="form.email" type="email" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">New Password <span class="text-gray-400 text-xs">(leave blank to keep current)</span></label>
        <input v-model="form.password" type="password" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
        <input v-model="form.password_confirmation" type="password" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400" />
      </div>
      <button
        type="submit"
        :disabled="busy"
        class="bg-gradient-to-r from-purple-500 to-purple-700 text-white py-2 rounded-lg font-medium hover:-translate-y-0.5 transition disabled:opacity-50"
      >
        {{ busy ? 'Saving...' : 'Save Changes' }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const form = ref({ name: '', email: '', password: '', password_confirmation: '' })
const busy = ref(false)
const successMsg = ref('')
const errorMsg = ref('')

onMounted(async () => {
  const { data } = await axios.get('/api/settings')
  form.value.name  = data.user.name
  form.value.email = data.user.email
})

const save = async () => {
  busy.value = true
  successMsg.value = ''
  errorMsg.value = ''
  try {
    const { data } = await axios.put('/api/settings', form.value)
    successMsg.value = data.message
    form.value.password = ''
    form.value.password_confirmation = ''
  } catch (e) {
    errorMsg.value = e.response?.data?.message ?? 'Something went wrong.'
  } finally {
    busy.value = false
  }
}
</script>