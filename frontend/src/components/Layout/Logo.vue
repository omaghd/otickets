<script setup lang="ts">
import LogoLottie from '@/assets/logo.json'

import { appTitle } from '@/global'

withDefaults(
  defineProps<{
    classNames?: string
    withText?: boolean
  }>(),
  {
    classNames: 'w-32',
    withText: false
  }
)
</script>

<template>
  <router-link :to="{ name: 'Home' }" class="flex items-center gap-1" :title="appTitle">
    <LottieAnimation :class="classNames" :animationData="LogoLottie" :loop="false" />

    <p v-if="withText" class="text-3xl font-semibold text-teal-600">
      {{ appTitle }}
    </p>
  </router-link>
</template>
