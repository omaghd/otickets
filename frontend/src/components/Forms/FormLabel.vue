<script setup lang="ts">
defineProps<{
  id?: string
  text: string
}>()
</script>

<template>
  <label :for="id" class="mb-1 block text-sm font-medium text-gray-700">
    {{ text }}
  </label>
</template>
