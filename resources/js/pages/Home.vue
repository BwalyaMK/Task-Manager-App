<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="text-center mt-10">
    <h1 class="text-3xl font-bold mb-6">Man I hope this works</h1>
    <div class="flex gap-4 justify-center">
      <Link href="/login" class="px-4 py-2 bg-blue-500 text-white rounded">
        Login
      </Link>
      <Link href="/register" class="px-4 py-2 bg-green-500 text-white rounded">
        Register
      </Link>
    </div>
  </div>
</template>
