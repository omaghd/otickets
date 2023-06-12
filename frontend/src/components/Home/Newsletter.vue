<script setup lang="ts">
import useNewsletters from '@/composables/newsletters/useNewsletters'

import { ref } from 'vue'

import { useToast } from 'vue-toastification'

const { save, isLoading, isSuccess, message } = useNewsletters()

const email = ref('')

const toast = useToast()

const subscribe = async () => {
  if (isLoading.value) return

  await save(email.value, 'post')

  if (isSuccess.value) {
    email.value = ''
    toast.success(message.value)
  } else {
    toast.info(message.value)
  }
}
</script>

<template>
  <div class="mb-6">
    <div class="relative">
      <div class="hidden md:block">
        <svg
          class="absolute top-8 left-1/2 -ml-3"
          width="404"
          height="392"
          fill="none"
          viewBox="0 0 404 392"
        >
          <defs>
            <pattern
              id="8228f071-bcee-4ec8-905a-2a059a2cc4fb"
              x="0"
              y="0"
              width="20"
              height="20"
              patternUnits="userSpaceOnUse"
            >
              <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor" />
            </pattern>
          </defs>
          <rect width="404" height="392" fill="url(#8228f071-bcee-4ec8-905a-2a059a2cc4fb)" />
        </svg>
      </div>
      <div class="mx-auto max-w-md sm:max-w-3xl lg:max-w-7xl">
        <div
          class="relative overflow-hidden rounded-2xl bg-teal-600 px-6 py-10 shadow-xl sm:px-12 sm:py-20"
        >
          <div class="absolute inset-0 -mt-72 sm:-mt-32 md:mt-0">
            <svg
              class="absolute inset-0 h-full w-full"
              preserveAspectRatio="xMidYMid slice"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 1463 360"
            >
              <path
                class="text-teal-500 text-opacity-40"
                fill="currentColor"
                d="M-82.673 72l1761.849 472.086-134.327 501.315-1761.85-472.086z"
              />
              <path
                class="text-teal-700 text-opacity-40"
                fill="currentColor"
                d="M-217.088 544.086L1544.761 72l134.327 501.316-1761.849 472.086z"
              />
            </svg>
          </div>
          <div class="relative">
            <div class="text-center">
              <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                Sign up for our newsletter
              </h2>
              <p class="mx-auto mt-6 max-w-2xl text-lg text-teal-100">
                Join our community of web app enthusiasts and receive expert tips, industry
                insights, and early access to new releases by signing up for our newsletter.
              </p>
            </div>
            <form class="mt-12 sm:mx-auto sm:flex sm:max-w-lg" @submit.prevent="subscribe">
              <div class="min-w-0 flex-1">
                <label for="cta-email" class="sr-only">Email address</label>
                <input
                  v-model="email"
                  id="cta-email"
                  type="email"
                  class="block w-full rounded-md border border-transparent px-5 py-3 text-base text-gray-900 placeholder-gray-500 shadow-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-teal-600"
                  placeholder="Enter your email"
                />
              </div>
              <div class="mt-4 sm:mt-0 sm:ml-3">
                <button
                  :disabled="isLoading"
                  type="submit"
                  class="block w-full rounded-md border border-transparent bg-teal-800 px-5 py-3 text-base font-medium text-white shadow hover:bg-teal-900 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-teal-600 disabled:opacity-50 sm:px-10"
                >
                  Stay Informed
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
