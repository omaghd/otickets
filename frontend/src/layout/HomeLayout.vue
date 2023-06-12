<script setup lang="ts">
import Navbar from '@/components/Layout/Navbar.vue'
import Footer from '@/components/Layout/Footer.vue'
</script>

<template>
  <div class="flex min-h-screen flex-col bg-gray-100">
    <Navbar />

    <router-view class="my-6 flex w-full flex-1 flex-col p-3" />

    <Footer />
  </div>
</template>
