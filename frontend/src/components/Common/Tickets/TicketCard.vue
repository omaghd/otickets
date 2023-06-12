<script setup lang="ts">
import SectionHeader from '@/components/Common/SectionHeader.vue'
import TicketInfo from '@/components/Common/Tickets/TicketInfo.vue'
import Attachments from './Attachments.vue'

import type Ticket from '@/types/Ticket'

defineProps<{ ticket: Ticket }>()
</script>

<template>
  <div class="divide-y rounded-lg border shadow">
    <div class="p-3">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="overflow-hidden">
          <SectionHeader :title="ticket.subject" />

          <TicketInfo
            class="overflow-x-auto whitespace-nowrap"
            :reference="ticket.reference"
            :priority="ticket.priority"
            :created-at="ticket.created_at"
            :category-name="ticket.category?.name"
            :last-reply-on="ticket.replies?.[0]?.created_at"
          />
        </div>

        <div>
          <span
            class="rounded-full px-3 py-px text-sm"
            :class="{
              'bg-indigo-100 text-indigo-800': ticket.status === 'assigned',
              'bg-gray-100 text-gray-800': ticket.status === 'unassigned',
              'bg-red-100 text-red-800': ticket.status === 'closed',
              'bg-green-100 text-green-800': ticket.status === 'resolved'
            }"
          >
            {{ ticket.status }}
          </span>
        </div>
      </div>
    </div>

    <div class="px-3 py-5">
      <article
        class="prose max-w-none space-y-0 prose-headings:mb-0"
        v-html="ticket.description"
      ></article>
    </div>

    <Attachments v-if="ticket.attachments?.length" class="p-3" :attachments="ticket.attachments" />
  </div>
</template>
