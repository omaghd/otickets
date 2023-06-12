<script setup lang="ts">
import ProfileForm from '@/components/Common/ProfileForm.vue'
import FormInput from '@/components/Forms/FormInput.vue'

import { appTitle } from '@/global'

import { useHead } from 'unhead'

import { useDashboard } from '@/stores/dashboard'
import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

const { setTitle } = useDashboard()

useHead({ title: `Profile | ${appTitle}` })

setTitle('Profile')

const authStore = useAuthStore()
const { user, isAgent } = storeToRefs(authStore)
</script>

<template>
  <ProfileForm action-class="mt-6">
    <FormInput
      v-if="isAgent"
      id="department"
      type="text"
      label="Department"
      :disabled="true"
      :value="user.department?.name"
    />
  </ProfileForm>
</template>
