<template>
  <div class="max-w-xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

      <div class="bg-gradient-to-r from-purple-500 to-purple-700 h-32 relative">
        <div class="absolute -bottom-12 left-1/2 -translate-x-1/2">
          <div class="w-24 h-24 rounded-full bg-white shadow-xl flex items-center justify-center border-4 border-purple-500">
            <span class="text-3xl font-bold text-purple-600">{{ initials }}</span>
          </div>
        </div>
      </div>

      <div class="pt-16 px-8 pb-8">
        <div class="text-center mb-6">
          <h2 class="text-2xl font-bold text-gray-800">{{ user.name }}</h2>
          <span class="inline-block mt-1 text-xs bg-purple-100 text-purple-700 px-3 py-1 rounded-full font-semibold uppercase tracking-wide">
            {{ user.role }}
          </span>
        </div>

        <div class="flex flex-col gap-3">
          <div class="flex items-center gap-4 bg-gray-50 rounded-xl px-5 py-4">
            <div class="text-xl">✉️</div>
            <div>
              <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Email</p>
              <p class="text-gray-800 font-medium">{{ user.email }}</p>
            </div>
          </div>
          <div class="flex items-center gap-4 bg-gray-50 rounded-xl px-5 py-4">
            <div class="text-xl">💙</div>
            <div>
              <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">GCash Number</p>
              <p class="text-gray-800 font-medium">{{ user.gcash_number || '—' }}</p>
            </div>
          </div>
          <div class="flex items-center gap-4 bg-gray-50 rounded-xl px-5 py-4">
            <div class="text-xl">📅</div>
            <div>
              <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Member Since</p>
              <p class="text-gray-800 font-medium">{{ joinedDate }}</p>
            </div>
          </div>
        </div>

        <router-link
          to="/settings"
          class="mt-6 block text-center bg-gradient-to-r from-purple-500 to-purple-700 text-white py-3 rounded-xl font-semibold hover:-translate-y-0.5 transition"
        >
          ✏️ Edit Profile
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const user = ref({ name: '', email: '', role: '', gcash_number: '', created_at: '' })

const initials = computed(() => {
  return user.value.name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2)
})

const joinedDate = computed(() => {
  if (!user.value.created_at) return '—'
  return new Date(user.value.created_at).toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' })
})

onMounted(async () => {
  const { data } = await axios.get('/lessor/api/me')
  user.value = data.user
})
</script>