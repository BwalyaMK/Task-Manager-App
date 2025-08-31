<script setup lang="js">
import { useForm } from '@inertiajs/vue3'
import GuestLayout from '@/layouts/GuestLayout.vue'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

function submit() {
  form.post('/register')
}
</script>

<template>
  <GuestLayout>
    <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
      <h2 class="text-xl font-bold mb-4">Register</h2>

      <form @submit.prevent="submit">
        <div class="mb-4">
          <label>Name</label>
          <input v-model="form.name" type="text" class="w-full border p-2 rounded" required />
          <div v-if="form.errors.name" class="text-red-600">{{ form.errors.name }}</div>
        </div>

        <div class="mb-4">
          <label>Email</label>
          <input v-model="form.email" type="email" class="w-full border p-2 rounded" required />
          <div v-if="form.errors.email" class="text-red-600">{{ form.errors.email }}</div>
        </div>

        <div class="mb-4">
          <label>Password</label>
          <input v-model="form.password" type="password" class="w-full border p-2 rounded" required />
          <div v-if="form.errors.password" class="text-red-600">{{ form.errors.password }}</div>
        </div>

        <div class="mb-4">
          <label>Confirm Password</label>
          <input v-model="form.password_confirmation" type="password" class="w-full border p-2 rounded" required />
        </div>

        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">
          Register
        </button>
      </form>
    </div>
  </GuestLayout>
</template>