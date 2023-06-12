<script setup lang="ts">
import Notifications from '@/components/Common/Notifications.vue'

import { useHead } from 'unhead'

import { appTitle } from '@/global'

import { useDashboard } from '@/stores/dashboard'

useHead({ title: `Notifications | ${appTitle}` })

const { setTitle } = useDashboard()

setTitle('Notifications')
</script>

<template>
  <Notifications notification-class="text-2xl font-semibold text-gray-900" :show-header="false" />
</template>
