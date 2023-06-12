<script setup lang="ts">
import CTALink from '@/components/Common/CTALink.vue'

import MagnifyingGlassIcon from '@heroicons/vue/24/outline/MagnifyingGlassIcon'

import HeroLottie from '@/assets/hero.json'

import { useSearchPalette } from '@/stores/searchPalette'

const { open } = useSearchPalette()
</script>

<template>
  <div>
    <section class="flex flex-col-reverse items-center justify-between gap-6 md:flex-row">
      <header class="">
        <div class="space-y-6">
          <h1
            class="text-center text-4xl font-extrabold tracking-tight sm:mt-5 sm:text-5xl md:text-left md:text-4xl lg:mt-6 lg:text-5xl xl:text-6xl"
          >
            <span class="block">Your satisfaction is</span>
            <span
              class="block bg-gradient-to-r from-teal-400 to-teal-900 bg-clip-text pb-3 text-transparent"
            >
              our top priority
            </span>
          </h1>
          <h2 class="text-center text-lg md:text-left lg:text-xl">
            Get the help you need with our FAQs and ticketing system
          </h2>
          <div class="flex items-center justify-center gap-3 md:justify-start">
            <button
              @click="open"
              class="inline-flex items-center justify-center gap-1 rounded-md border border-teal-600 px-4 py-3 font-medium text-teal-600 shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 sm:w-auto"
            >
              <MagnifyingGlassIcon class="h-5 w-5" />
              <span>Search</span>
            </button>

            <CTALink to="ClientNewTicket" text="Submit a ticket" />
          </div>
        </div>
      </header>

      <LottieAnimation
        :animationData="HeroLottie"
        class="w-[500px] max-w-full md:w-[550px] lg:flex-1"
        :loop="false"
      />
    </section>
  </div>
</template>
