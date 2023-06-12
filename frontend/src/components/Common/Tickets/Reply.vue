<script setup lang="ts">
import Attachments from './Attachments.vue'

import type Reply from '@/types/Reply'

import { computed } from 'vue'

import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'

const props = defineProps<{ reply: Reply }>()

const API_URL = import.meta.env.VITE_API_URL

const store = useAuthStore()
const { user } = storeToRefs(store)

const userName = computed(() =>
  props.reply.user.id == user.value.id ? 'You' : props.reply.user.name
)

const isAgentReply = computed(() => props.reply.user.role == 'agent')

const picture = computed(() => API_URL + props.reply.user.picture)
</script>

<template>
  <div class="divide-y rounded-lg border shadow">
    <div class="flex gap-2 p-3">
      <div class="rounded-full">
        <img
          :src="picture"
          alt="Profile Picture"
          class="h-14 w-14 rounded-full border object-cover"
        />
      </div>

      <div>
        <div class="text-sm">{{ userName }}</div>
        <div class="text-xs text-gray-500">
          <div v-if="isAgentReply">{{ reply.user.department?.name }}</div>
          <div>{{ reply.created_at }}</div>
        </div>
      </div>
    </div>

    <article
      class="prose max-w-none space-y-0 px-3 py-5 prose-headings:mb-0"
      v-html="reply.content"
    ></article>

    <Attachments v-if="reply.attachments.length" class="p-3" :attachments="reply.attachments" />
  </div>
</template>
