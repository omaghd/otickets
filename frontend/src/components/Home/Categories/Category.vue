<script setup lang="ts">
import type Category from '@/types/Category'

defineProps<{
  category: Category
}>()
</script>

<template>
  <router-link
    class="flex items-center rounded-lg border bg-white hover:bg-teal-600 hover:text-white"
    :to="{ name: 'HomeCategory', params: { slug: category.slug } }"
  >
    <div class="flex-1 p-6 text-center">
      <div>
        <span class="text-xl font-semibold">
          {{ category.name }}
        </span>
        ({{ category.faqs_count }})
      </div>
    </div>
  </router-link>
</template>
