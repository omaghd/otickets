<script setup lang="ts">
import PaperClipIcon from '@heroicons/vue/24/outline/PaperClipIcon'

import type Attachment from '@/types/Attachment'

defineProps<{ attachments: Attachment[] }>()

const API_URL = import.meta.env.VITE_API_URL
</script>

<template>
  <div>
    <div class="flex items-center gap-1 font-semibold text-gray-500">
      <PaperClipIcon class="h-4 w-4" />
      <h2>Attachments</h2>
    </div>

    <div class="mt-3 flex flex-wrap gap-3">
      <a
        v-for="attachment in attachments"
        :key="attachment.id"
        :href="API_URL + attachment.path"
        target="_blank"
      >
        <img
          :src="API_URL + attachment.path"
          :alt="attachment.filename"
          class="h-20 w-20 rounded-lg border object-cover"
        />
      </a>
    </div>
  </div>
</template>
