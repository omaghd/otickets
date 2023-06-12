<script setup lang="ts">
defineProps<{
  type: 'button' | 'submit'
  text: string
  loading?: boolean
}>()
</script>

<template>
  <button
    :disabled="loading"
    :type="type"
    :class="{ 'opacity-50': loading }"
    class="items-center rounded-md border border-transparent bg-teal-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
  >
    <div class="flex justify-center" v-if="loading">
      <svg
        class="h-6 w-6 animate-spin text-white"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4"
        ></circle>
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        ></path>
      </svg>
    </div>

    <span v-else>{{ text }}</span>
  </button>
</template>
