<script setup lang="ts">
defineProps<{
  action: Function
  text: string
  Icon: any
}>()
</script>

<template>
  <button
    @click="action()"
    type="button"
    class="inline-flex items-center gap-2 rounded-md border border-transparent bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
  >
    <component :is="Icon" class="-ml-1 h-5 w-5 text-white" />
    <div>{{ text }}</div>
  </button>
</template>
