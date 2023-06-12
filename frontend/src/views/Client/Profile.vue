<script setup lang="ts">
import ProfileForm from '@/components/Common/ProfileForm.vue'
import SectionHeader from '@/components/Common/SectionHeader.vue'

import { appTitle } from '@/global'

import { useHead } from 'unhead'

useHead({ title: `Profile | ${appTitle}` })
</script>

<template>
  <ProfileForm
    form-class="px-4 py-6 sm:p-6 lg:pb-8"
    action-class="flex justify-end bg-gray-50 py-4 px-4 sm:px-6 border-t"
  >
    <SectionHeader class="mb-6" title="Profile" />
  </ProfileForm>
</template>
