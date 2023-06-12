<script setup lang="ts">
import FormInput from '@/components/Forms/FormInput.vue'
import PrimaryButton from '@/components/Forms/PrimaryButton.vue'
import Logo from '@/components/Layout/Logo.vue'

import EnvelopeIcon from '@heroicons/vue/24/outline/EnvelopeIcon'
import ArrowLeftIcon from '@heroicons/vue/24/outline/ArrowLeftIcon'

import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

import { ref } from 'vue'

import { useHead } from 'unhead'

import { appTitle } from '@/global'

import { useToast } from 'vue-toastification'

useHead({ title: `Forgot Password | ${appTitle}` })

const email = ref('')

const store = useAuthStore()

const { errors, isLoading, message, isSuccess } = storeToRefs(store)

const { forgotPassword } = store

const toast = useToast()

const onSubmit = async () => {
  if (isLoading.value) return

  await forgotPassword(email.value)

  if (isSuccess.value) toast.success(message.value)
  else toast.error(message.value)
}
</script>

<template>
  <section class="mx-auto max-w-md justify-center">
    <div class="rounded-md border bg-white px-6 pt-6 pb-8 shadow">
      <header class="mb-6">
        <Logo />

        <h2 class="text-center text-lg">Forgot your password?</h2>
      </header>

      <form @submit.prevent="onSubmit">
        <FormInput
          id="email"
          type="email"
          label="Email"
          placeholder="email@example.com"
          @change="(value) => (email = value)"
          :errors="errors?.email"
          :Icon="EnvelopeIcon"
        />

        <PrimaryButton type="submit" text="Send" class="mt-8 w-full" :loading="isLoading" />
      </form>

      <div class="mt-8">
        <router-link :to="{ name: 'Login' }" class="text-teal-600 hover:text-teal-800">
          <p class="flex items-center justify-center gap-1">
            <ArrowLeftIcon class="h-5 w-5" />
            Back to Login
          </p>
        </router-link>
      </div>
    </div>
  </section>
</template>
