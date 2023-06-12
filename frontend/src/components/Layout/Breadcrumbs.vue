<script setup lang="ts">
import HomeIcon from '@heroicons/vue/24/outline/HomeIcon'
import ChevronRightIcon from '@heroicons/vue/24/solid/ChevronRightIcon'

import type Page from '@/types/Page'

defineProps<{ pages?: Page[] }>()
</script>

<template>
  <nav class="flex">
    <ol role="list" class="flex items-center space-x-4">
      <li>
        <div>
          <router-link :to="{ name: 'Home' }" class="text-gray-400 hover:text-gray-500">
            <HomeIcon class="h-5 w-5 flex-shrink-0" />
            <span class="sr-only">Home</span>
          </router-link>
        </div>
      </li>
      <li v-for="page in pages" :key="page.name">
        <div class="flex items-center">
          <ChevronRightIcon class="h-5 w-5 flex-shrink-0 text-gray-400" />
          <router-link
            :to="{ name: page.to, params: page.params }"
            class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
          >
            {{ page.name }}
          </router-link>
        </div>
      </li>
    </ol>
  </nav>
</template>
