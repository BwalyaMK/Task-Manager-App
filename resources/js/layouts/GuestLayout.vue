<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="min-h-screen flex flex-col justify-center items-center bg-gray-100">
    <!-- Navbar -->
    <nav class="w-full flex justify-end p-4 bg-white shadow">
      <Link href="/login" class="px-4 py-2 bg-blue-500 text-white rounded mx-2">Login</Link>
      <Link href="/register" class="px-4 py-2 bg-green-500 text-white rounded mx-2">Register</Link>
    </nav>

    <!-- Main Page Content -->
    <main class="w-full max-w-2xl mt-8">
      <slot />
    </main>
  </div>
</template>
