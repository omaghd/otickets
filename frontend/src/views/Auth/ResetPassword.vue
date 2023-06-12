<script setup lang="ts">
import FormInput from '@/components/Forms/FormInput.vue'
import PrimaryButton from '@/components/Forms/PrimaryButton.vue'
import Logo from '@/components/Layout/Logo.vue'

import LockClosedIcon from '@heroicons/vue/24/outline/LockClosedIcon'

import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

import { ref } from 'vue'

import { useRoute, useRouter } from 'vue-router'

import { useHead } from 'unhead'

import { appTitle } from '@/global'

import { useToast } from 'vue-toastification'

useHead({ title: `Reset Password | ${appTitle}` })

const password = ref('')
const passwordConfirmation = ref('')

const store = useAuthStore()

const { errors, isLoading, message, isSuccess } = storeToRefs(store)

const { resetPassword } = store

const route = useRoute()

const toast = useToast()

const router = useRouter()

const onSubmit = async () => {
  if (isLoading.value) return

  await resetPassword({
    password: password.value,
    password_confirmation: passwordConfirmation.value,
    email: route.query.email?.toString(),
    token: route.query.token?.toString()
  })

  if (isSuccess.value) {
    router.push({ name: 'Login' })

    toast.success(message.value)
  } else {
    toast.error(message.value)
  }
}
</script>

<template>
  <section class="mx-auto max-w-md justify-center">
    <div class="rounded-md border bg-white px-6 pt-6 pb-8 shadow">
      <header class="mb-6">
        <Logo />

        <h2 class="text-center text-lg">Set a new password</h2>
      </header>

      <form @submit.prevent="onSubmit">
        <div class="space-y-3">
          <FormInput
            id="password"
            type="password"
            label="Password"
            placeholder="********"
            @change="(value) => (password = value)"
            :errors="errors?.password"
            :Icon="LockClosedIcon"
          />

          <FormInput
            id="password_confirmation"
            type="password"
            label="Password Confirmation"
            placeholder="********"
            @change="(value) => (passwordConfirmation = value)"
            :Icon="LockClosedIcon"
          />
        </div>

        <PrimaryButton
          type="submit"
          text="Change Password"
          class="mt-8 w-full"
          :loading="isLoading"
        />
      </form>
    </div>
  </section>
</template>
