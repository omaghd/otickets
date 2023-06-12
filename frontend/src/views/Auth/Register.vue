<script setup lang="ts">
import FormInput from '@/components/Forms/FormInput.vue'
import PrimaryButton from '@/components/Forms/PrimaryButton.vue'
import Logo from '@/components/Layout/Logo.vue'

import UserIcon from '@heroicons/vue/24/outline/UserIcon'
import PhoneIcon from '@heroicons/vue/24/outline/PhoneIcon'
import EnvelopeIcon from '@heroicons/vue/24/outline/EnvelopeIcon'
import LockClosedIcon from '@heroicons/vue/24/outline/LockClosedIcon'

import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

import { ref } from 'vue'

import { useHead } from 'unhead'

import { appTitle } from '@/global'

import { useToast } from 'vue-toastification'

import { useRouter } from 'vue-router'

useHead({ title: `Register | ${appTitle}` })

const name = ref('')
const email = ref('')
const phone = ref('')
const password = ref('')
const passwordConfirmation = ref('')

const store = useAuthStore()

const { errors, isLoading, message, isSuccess } = storeToRefs(store)

const { register } = store

const toast = useToast()

const router = useRouter()

const onSubmit = async () => {
  if (isLoading.value) return

  await register({
    name: name.value,
    email: email.value,
    phone: phone.value,
    password: password.value,
    password_confirmation: passwordConfirmation.value
  })

  if (isSuccess.value) router.push({ name: 'ClientTickets' })
  else toast.error(message.value)
}
</script>

<template>
  <section class="mx-auto max-w-md justify-center">
    <div class="rounded-md border bg-white px-6 pt-6 pb-8 shadow">
      <header class="mb-6">
        <Logo />

        <h2 class="text-center text-lg">Register for an account</h2>
      </header>

      <form @submit.prevent="onSubmit">
        <div class="space-y-3">
          <FormInput
            id="name"
            type="text"
            label="Full Name"
            placeholder="John Doe"
            @change="(value) => (name = value)"
            :errors="errors?.name"
            :Icon="UserIcon"
          />

          <FormInput
            id="phone"
            type="tel"
            label="Phone Number"
            placeholder="0612345678"
            @change="(value) => (phone = value)"
            :errors="errors?.phone"
            :Icon="PhoneIcon"
          />

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

          <FormInput
            id="password_confirmation"
            type="password"
            label="Password Confirmation"
            placeholder="********"
            @change="(value) => (passwordConfirmation = value)"
            :Icon="LockClosedIcon"
          />
        </div>

        <PrimaryButton type="submit" text="Register" class="mt-8 w-full" :loading="isLoading" />
      </form>

      <div class="mt-8">
        <p class="text-center text-gray-500">
          Already have an account?
          <router-link :to="{ name: 'Login' }" class="text-teal-600">Sign in</router-link>
        </p>
      </div>
    </div>
  </section>
</template>
