<script setup lang="ts">
defineProps<{
  to: string
  text: string
  Icon?: any
}>()
</script>

<template>
  <router-link
    :to="{ name: to }"
    class="inline-flex items-center justify-center rounded-md border border-transparent bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 sm:w-auto"
  >
    <component v-if="Icon" :is="Icon" class="-ml-1 mr-3 h-5 w-5" />
    {{ text }}
  </router-link>
</template>
