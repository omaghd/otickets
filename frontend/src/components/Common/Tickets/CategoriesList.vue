<script setup lang="ts">
import SectionHeader from '@/components/Common/SectionHeader.vue'

import type Category from '@/types/Category'

defineProps<{ categories: Category[] }>()
</script>

<template>
  <div>
    <SectionHeader title="Categories" class="mb-2" />

    <div class="divide-y overflow-hidden rounded-lg border bg-white">
      <router-link
        v-for="category in categories"
        :key="category.id"
        :to="{ name: 'HomeCategory', params: { slug: category.slug } }"
        class="flex justify-between px-4 py-3 hover:bg-gray-50"
      >
        <p>{{ category.name }}</p>
        <p
          class="inline-flex items-center rounded-full bg-teal-50 px-2.5 py-0.5 text-xs font-medium text-teal-800"
        >
          {{ category.faqs_count }}
        </p>
      </router-link>
    </div>
  </div>
</template>
