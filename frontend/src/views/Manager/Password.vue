<script setup lang="ts">
import PasswordForm from '@/components/Common/PasswordForm.vue'

import { appTitle } from '@/global'

import { useHead } from 'unhead'

import { useDashboard } from '@/stores/dashboard'

const { setTitle } = useDashboard()

useHead({ title: `Password | ${appTitle}` })

setTitle('Password')
</script>

<template>
  <PasswordForm action-class="mt-6" />
</template>
