<script setup lang="ts">
import PasswordForm from '@/components/Common/PasswordForm.vue'
import SectionHeader from '@/components/Common/SectionHeader.vue'

import { useHead } from 'unhead'

import { appTitle } from '@/global'

useHead({ title: `Change Password | ${appTitle}` })
</script>

<template>
  <PasswordForm
    form-class="px-4 py-6 sm:p-6 lg:pb-8"
    action-class="flex justify-end bg-gray-50 py-4 px-4 sm:px-6 border-t"
  >
    <SectionHeader class="mb-6" title="Password" />
  </PasswordForm>
</template>
