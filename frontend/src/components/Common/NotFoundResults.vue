<script setup lang="ts">
import EmptyResultsLottie from '@/assets/emptyResults.json'

defineProps<{ text: string }>()
</script>

<template>
  <div>
    <LottieAnimation class="w-60 max-w-full" :animationData="EmptyResultsLottie" :loop="false" />
    <p class="text-center text-xl">{{ text }}</p>
  </div>
</template>
