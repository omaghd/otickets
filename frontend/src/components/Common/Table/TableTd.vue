<script setup lang="ts">
defineProps<{
  noPadding?: boolean
}>()
</script>

<template>
  <td :class="{ 'px-3 py-4': !noPadding }" class="whitespace-nowrap text-sm text-gray-500">
    <slot />
  </td>
</template>
