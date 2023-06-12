<script setup lang="ts">
import SectionHeader from '@/components/Common/SectionHeader.vue'
import FormInput from '@/components/Forms/FormInput.vue'
import PrimaryButton from '@/components/Forms/PrimaryButton.vue'

import LockClosedIcon from '@heroicons/vue/24/outline/LockClosedIcon'

import { ref } from 'vue'

import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

import { useToast } from 'vue-toastification'

import { onBeforeRouteLeave } from 'vue-router'

defineProps<{
  formClass?: string
  actionClass?: string
}>()

const currentPassword = ref('')
const newPassword = ref('')
const newPasswordConfirmation = ref('')

const reset = ref(false)

const store = useAuthStore()
const { errors, isSuccess, message, isLoading } = storeToRefs(store)

const toast = useToast()

const onSubmit = async () => {
  if (isLoading.value) return

  await store.updatePassword({
    current_password: currentPassword.value,
    password: newPassword.value,
    password_confirmation: newPasswordConfirmation.value
  })

  if (isSuccess.value) {
    toast.success(message.value)

    reset.value = true
  } else {
    toast.error(message.value)
  }
}

onBeforeRouteLeave(() => {
  errors.value = {}
})
</script>

<template>
  <form @submit.prevent="onSubmit">
    <div :class="formClass">
      <slot></slot>

      <div class="flex flex-col gap-12 sm:flex-row sm:items-center">
        <div class="w-full space-y-6">
          <FormInput
            @change="(value) => (currentPassword = value)"
            :Icon="LockClosedIcon"
            placeholder="Current Password"
            label="Current Password"
            type="password"
            id="current-password"
            :errors="errors?.current_password"
            :reset="reset"
            @reset="() => (reset = false)"
          />

          <FormInput
            @change="(value) => (newPassword = value)"
            :Icon="LockClosedIcon"
            placeholder="New Password"
            label="New Password"
            type="password"
            id="new-password"
            :errors="errors?.password"
            :reset="reset"
            @reset="() => (reset = false)"
          />

          <FormInput
            @change="(value) => (newPasswordConfirmation = value)"
            :Icon="LockClosedIcon"
            placeholder="Confirm Password"
            label="Confirm Password"
            type="password"
            id="new-password-confirmation"
            :errors="errors?.password_confirmation"
            :reset="reset"
            @reset="() => (reset = false)"
          />
        </div>

        <div
          class="h-fit w-full max-w-fit rounded-lg border border-amber-400 bg-amber-50 p-6 shadow"
        >
          <SectionHeader class="mb-5" title="Password Guidelines" />
          <ul class="list-inside list-disc space-y-1 text-gray-700">
            <li>Contains at least 8 characters long</li>
            <li>Includes at least one uppercase letter</li>
            <li>Includes at least one lowercase letter</li>
            <li>Includes at least one number</li>
            <li>Includes at least one special character</li>
          </ul>
        </div>
      </div>
    </div>

    <div :class="actionClass">
      <PrimaryButton type="submit" text="Update" :loading="isLoading" />
    </div>
  </form>
</template>
