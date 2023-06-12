<script setup lang="ts">
withDefaults(
  defineProps<{
    title: string
    customClass?: string
  }>(),
  { customClass: 'text-xl font-semibold' }
)
</script>

<template>
  <header>
    <h1 :class="customClass">
      {{ title }}
    </h1>
  </header>
</template>
