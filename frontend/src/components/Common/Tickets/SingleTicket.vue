<script setup lang="ts">
import TicketSkeleton from '@/components/Common/Tickets/TicketSkeleton.vue'
import TicketCard from '@/components/Common/Tickets/TicketCard.vue'
import SectionHeader from '@/components/Common/SectionHeader.vue'
import ReplySkeleton from '@/components/Common/Tickets/ReplySkeleton.vue'
import Reply from '@/components/Common/Tickets/Reply.vue'
import NewReply from '@/components/Common/Tickets/NewReply.vue'

import { useHead } from 'unhead'

import { appTitle } from '@/global'

import getTicket from '@/composables/tickets/getTicket'

import { onBeforeRouteUpdate, useRoute, useRouter } from 'vue-router'

import { onMounted, ref } from 'vue'

import type Ticket from '@/types/Ticket'

import notFound from '@/util/notFound'
import { useAuthStore } from '@/stores/auth'

defineProps<{ customClass?: string }>()

const route = useRoute()

const ticket = ref({} as Ticket)
const isLoading = ref(true)

const isNotResolved = ref(false)

const { user, isClient } = useAuthStore()

const isCurrentAgent = ref(false)

const router = useRouter()

const fetchTicket = async (afterReply = false, reference = route.params.reference as string) => {
  isLoading.value = true

  const result = await getTicket(reference)

  if (result.error.value) notFound(route)

  ticket.value = result.ticket.value
  isLoading.value = result.isLoading.value

  isCurrentAgent.value = ticket.value.agents?.[0]?.id === user.id

  isNotResolved.value = ticket.value.status !== 'resolved' && ticket.value.status !== 'closed'

  useHead({ title: `${ticket.value.subject} | ${appTitle}` })

  if (afterReply) {
    router.push({ hash: '#conversation' })
  }
}

onMounted(async () => {
  await fetchTicket(false)
})

onBeforeRouteUpdate(async (to) => {
  await fetchTicket(false, to.params.reference as string)
})
</script>

<template>
  <section :class="customClass" class="space-y-6">
    <div>
      <TicketSkeleton v-if="isLoading" />
      <TicketCard :ticket="ticket" v-else />
    </div>

    <div v-if="!isLoading && isNotResolved && (isCurrentAgent || isClient)">
      <NewReply :ticket-id="ticket.id" @reload="fetchTicket(true)" />
    </div>

    <div id="conversation" v-if="isLoading || ticket.replies?.length">
      <SectionHeader title="Conversation" class="mb-2" />

      <div class="space-y-4">
        <template v-if="isLoading">
          <ReplySkeleton v-for="i in 3" :key="i" />
        </template>

        <Reply :reply="reply" v-for="reply in ticket.replies" :key="reply.id" v-else />
      </div>
    </div>
  </section>
</template>
