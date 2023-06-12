<script setup lang="ts">
import { useHead } from 'unhead'

import { appTitle } from '@/global'

useHead({ title: `Not Found | ${appTitle}` })
</script>

<template>
  <div
    class="justify-center px-4 text-center sm:px-6 sm:text-left md:grid md:place-items-center lg:px-8"
  >
    <div class="mx-auto max-w-max">
      <main class="sm:flex">
        <p class="text-4xl font-extrabold text-teal-600 sm:text-5xl">404</p>
        <div class="sm:ml-6">
          <div class="sm:border-l sm:border-gray-200 sm:pl-6">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
              Page not found
            </h1>
            <p class="mt-1 text-base text-gray-500">
              Please check the URL in the address bar and try again.
            </p>
          </div>
          <div class="mt-10 sm:pl-6">
            <router-link
              :to="{ name: 'Home' }"
              class="inline-flex items-center rounded-md border border-transparent bg-teal-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
            >
              Go back home
            </router-link>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>
