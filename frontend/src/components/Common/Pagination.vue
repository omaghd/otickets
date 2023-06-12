<script setup lang="ts">
import ChevronLeftIcon from '@heroicons/vue/24/solid/ChevronLeftIcon'
import ChevronRightIcon from '@heroicons/vue/24/solid/ChevronRightIcon'

defineProps<{
  from?: number
  to?: number
  total?: number
  prev_page_url?: string
  next_page_url?: string
  links?: {
    url: string
    label: string
    active: boolean
  }[]
  routeName: string
  query?: any
}>()
</script>

<template>
  <div class="flex select-none items-center justify-between">
    <div class="flex flex-1 justify-between sm:hidden">
      <component
        :is="prev_page_url ? 'router-link' : 'span'"
        :to="{
          name: routeName,
          query: { page: prev_page_url?.charAt(prev_page_url.length - 1), ...query }
        }"
        :class="{ 'opacity-50': !prev_page_url }"
        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
      >
        Previous
      </component>

      <component
        :is="next_page_url ? 'router-link' : 'span'"
        :to="{
          name: routeName,
          query: { page: next_page_url?.charAt(next_page_url.length - 1), ...query }
        }"
        :class="{ 'opacity-50': !next_page_url }"
        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
      >
        Next
      </component>
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          Showing
          {{ ' ' }}
          <span class="font-medium">{{ from }}</span>
          {{ ' ' }}
          to
          {{ ' ' }}
          <span class="font-medium">{{ to }}</span>
          {{ ' ' }}
          of
          {{ ' ' }}
          <span class="font-medium">{{ total }}</span>
          {{ ' ' }}
          results
        </p>
      </div>
      <div>
        <nav class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm">
          <div v-for="link in links" :key="link.label">
            <component
              :is="!link.url ? 'span' : 'router-link'"
              v-if="link.label.includes('Previous')"
              :to="{
                name: routeName,
                query: { page: link.url?.charAt(link.url.length - 1), ...query }
              }"
              :class="{ 'opacity-50': !link.url }"
              class="relative inline-flex items-center rounded-l-md border bg-white px-2 py-2 text-sm font-medium"
            >
              <ChevronLeftIcon class="h-5 w-5 text-gray-400" />
            </component>

            <component
              :is="!link.url ? 'span' : 'router-link'"
              v-else-if="link.label.includes('Next')"
              :to="{
                name: routeName,
                query: { page: link.url?.charAt(link.url.length - 1), ...query }
              }"
              :class="{ 'opacity-50': !link.url }"
              class="relative inline-flex items-center rounded-r-md border bg-white px-2 py-2 text-sm font-medium"
            >
              <ChevronRightIcon class="h-5 w-5 text-gray-400" />
            </component>

            <component
              :is="link.active ? 'span' : 'router-link'"
              v-else
              :to="{ name: routeName, query: { page: link.label, ...query } }"
              :class="{
                'z-10 border-teal-500 bg-teal-50 text-teal-600': link.active,
                'border-gray-300 bg-white text-gray-500 hover:bg-gray-50': !link.active
              }"
              class="relative inline-flex items-center border px-4 py-2 text-sm font-medium"
            >
              {{ link.label }}
            </component>
          </div>
        </nav>
      </div>
    </div>
  </div>
</template>
