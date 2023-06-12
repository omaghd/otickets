<script setup lang="ts">
import StatSkeleton from '@/components/Common/StatSkeleton.vue'
import MinimalStat from '@/components/Common/MinimalStat.vue'
import AgentsTicketsByPriority from '@/components/Manager/Charts/AgentsTicketsByPriority.vue'
import AgentsResponseTime from '@/components/Manager/Charts/AgentsResponseTime.vue'
import TicketsCountsByStatus from '@/components/Manager/Charts/TicketsCountsByStatus.vue'
import Tickets from '@/components/Manager/Charts/Tickets.vue'
import Clients from '@/components/Manager/Charts/Clients.vue'
import TicketsCountsByCategory from '@/components/Manager/Charts/TicketsCountsByCategory.vue'

import { useAuthStore } from '@/stores/auth'
import { useDashboard } from '@/stores/dashboard'
import { storeToRefs } from 'pinia'

import { onMounted } from 'vue'

import getTicketsStats from '@/composables/tickets/getTicketsStats'
import getTicketsCountsByStatus from '@/composables/tickets/getTicketsCountsByStatus'
import getTicketsCountsByCategory from '@/composables/tickets/getTicketsCountsByCategory'
import getClientsStats from '@/composables/clients/getClientsStats'
import getTicketsCountsByAgentAndPriority from '@/composables/agents/getTicketsCountsByAgentAndPriority'
import getAgentsResponseTime from '@/composables/agents/getAgentsResponseTime'

import { useHead } from 'unhead'

import { appTitle } from '@/global'

useHead({ title: `Dashboard | ${appTitle}` })

const { setTitle } = useDashboard()

setTitle('Dashboard')

const authStore = useAuthStore()
const { isAdmin } = storeToRefs(authStore)

const { load: loadTicketsStats, ticketsStats } = getTicketsStats()
const {
  load: loadTicketsCountsByStatus,
  ticketsCountsByStatus,
  isLoading: ticketsCountsByStatusAreLoading
} = getTicketsCountsByStatus()
const { load: loadTicketsCountsByCategory, ticketsCountsByCategory } = getTicketsCountsByCategory()
const { load: loadClientsStats, clientsStats } = getClientsStats()
const { load: loadTicketsCountsByAgentAndPriority, ticketsCountsByAgentAndPriority } =
  getTicketsCountsByAgentAndPriority()
const { load: loadAgentsResponseTime, agentsResponseTime } = getAgentsResponseTime()

onMounted(async () => {
  await loadTicketsCountsByStatus()
  await loadTicketsStats()
  if (isAdmin.value) {
    await loadClientsStats()
    await loadAgentsResponseTime()
  }
  await loadTicketsCountsByCategory()

  if (isAdmin.value) {
    await loadTicketsCountsByAgentAndPriority()
  }
})
</script>

<template>
  <div class="mb-6 grid grid-cols-2 gap-6 xl:grid-cols-5">
    <template v-if="ticketsCountsByStatusAreLoading">
      <StatSkeleton class="first:col-span-2 xl:first:col-span-1" v-for="i in 5" :key="i" />
    </template>

    <router-link
      class="first:col-span-2 xl:first:col-span-1"
      v-for="count in ticketsCountsByStatus"
      :key="count.name"
      v-tooltip.bottom="`View ${count.name}`"
      :to="{
        name: 'DashboardTickets',
        query:
          count.name != 'Total Tickets'
            ? { status: count.name.toLowerCase().replace(' tickets', '') }
            : {}
      }"
      v-else
    >
      <MinimalStat :item="count" />
    </router-link>
  </div>

  <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
    <div :class="{ 'xl:col-span-2': !isAdmin }" class="rounded-lg border p-4 shadow">
      <p>Tickets</p>
      <Tickets :tickets="ticketsStats" />
    </div>

    <div class="rounded-lg border p-4 shadow" v-if="isAdmin">
      <p>Clients</p>
      <Clients :clients="clientsStats" />
    </div>

    <div class="rounded-lg border p-4 shadow" v-if="isAdmin">
      <p>Agents Response Time</p>
      <AgentsResponseTime :agents-response-time="agentsResponseTime" />
    </div>

    <div class="rounded-lg border p-4 shadow">
      <p>Tickets by Status</p>
      <TicketsCountsByStatus :tickets-counts-by-status="ticketsCountsByStatus" />
    </div>

    <div class="rounded-lg border p-4 shadow">
      <p>Tickets by Category</p>
      <TicketsCountsByCategory :tickets-counts-by-category="ticketsCountsByCategory" />
    </div>

    <div class="rounded-lg border p-4 shadow" v-if="isAdmin">
      <p>Agents Tickets By Priority</p>
      <AgentsTicketsByPriority
        :tickets-counts-by-agent-and-priority="ticketsCountsByAgentAndPriority"
      />
    </div>
  </div>
</template>
