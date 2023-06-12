<script setup lang="ts">
import FormInput from '@/components/Forms/FormInput.vue'
import PrimaryButton from '@/components/Forms/PrimaryButton.vue'
import Logo from '@/components/Layout/Logo.vue'

import EnvelopeIcon from '@heroicons/vue/24/outline/EnvelopeIcon'
import LockClosedIcon from '@heroicons/vue/24/outline/LockClosedIcon'

import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

import { ref } from 'vue'

import { useHead } from 'unhead'

import { appTitle } from '@/global'

import { useToast } from 'vue-toastification'

import { useRouter } from 'vue-router'

useHead({ title: `Login | ${appTitle}` })

const email = ref<string>('')
const password = ref<string>('')

const store = useAuthStore()

const { errors, isLoading, message, isSuccess, isClient } = storeToRefs(store)

const { login } = store

const toast = useToast()

const router = useRouter()

const onSubmit = async () => {
  if (isLoading.value) return

  await login({
    email: email.value,
    password: password.value
  })

  if (isSuccess.value) {
    if (isClient.value) router.push({ name: 'ClientTickets' })
    else router.push({ name: 'Dashboard' })
  } else {
    toast.error(message.value)
  }
}
</script>

<template>
  <section class="mx-auto max-w-md justify-center">
    <div class="rounded-lg border bg-white px-6 pt-6 pb-8 shadow">
      <header class="mb-6">
        <Logo />

        <h2 class="text-center text-lg">Sign in to your account</h2>
      </header>

      <form @submit.prevent="onSubmit">
        <div class="space-y-3">
          <FormInput
            id="email"
            type="email"
            label="Email"
            placeholder="email@example.com"
            @change="(value) => (email = value)"
            :errors="errors?.email"
            :Icon="EnvelopeIcon"
          />

          <FormInput
            id="password"
            type="password"
            label="Password"
            placeholder="********"
            @change="(value) => (password = value)"
            :errors="errors?.password"
            :Icon="LockClosedIcon"
          />
        </div>

        <router-link
          :to="{ name: 'ForgotPassword' }"
          class="mt-3 block text-right text-teal-600 hover:text-teal-800"
        >
          Forgot password?
        </router-link>

        <PrimaryButton type="submit" text="Login" class="mt-8 w-full" :loading="isLoading" />
      </form>

      <div class="mt-8">
        <p class="text-center text-gray-500">
          Don't have an account?
          <router-link :to="{ name: 'Register' }" class="text-teal-600 hover:text-teal-800">
            Register
          </router-link>
        </p>
      </div>
    </div>
  </section>
</template>
