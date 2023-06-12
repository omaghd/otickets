<script setup lang="ts">
defineProps<{ errors: Array<string> }>()
</script>

<template>
  <ul>
    <li v-for="error in errors" :key="error" class="mt-1 text-sm text-red-600">
      {{ error }}
    </li>
  </ul>
</template>
