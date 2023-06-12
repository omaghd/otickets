<script setup lang="ts">
import Notifications from '@/components/Common/Notifications.vue'

import { useHead } from 'unhead'

import { appTitle } from '@/global'

useHead({ title: `Notifications | ${appTitle}` })
</script>

<template>
  <Notifications custom-class="py-6 px-4 sm:p-6 lg:pb-8" />
</template>
