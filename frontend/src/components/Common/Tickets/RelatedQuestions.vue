<script setup lang="ts">
import SectionHeader from '@/components/Common/SectionHeader.vue'

import type Faq from '@/types/Faq'

defineProps<{ faqs: Faq[] }>()
</script>

<template>
  <div>
    <SectionHeader title="Related Questions" class="mb-2" />

    <div class="divide-y rounded-lg border bg-white">
      <router-link
        v-for="faq in faqs"
        :key="faq.id"
        :to="{ name: 'SingleFaq', params: { slug: faq.slug } }"
        class="block px-4 py-3 hover:bg-gray-50"
      >
        {{ faq.question }}
      </router-link>
    </div>
  </div>
</template>
