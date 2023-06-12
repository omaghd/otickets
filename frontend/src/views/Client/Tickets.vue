<script setup lang="ts">
import TableCard from '@/components/Common/Table/TableCard.vue'
import TableTd from '@/components/Common/Table/TableTd.vue'
import Pagination from '@/components/Common/Pagination.vue'
import FilterTickets from '@/components/Common/Tickets/FilterTickets.vue'
import SectionHeader from '@/components/Common/SectionHeader.vue'
import MinimalStat from '@/components/Common/MinimalStat.vue'
import StatSkeleton from '@/components/Common/StatSkeleton.vue'
import TicketInfo from '@/components/Common/Tickets/TicketInfo.vue'
import PrimaryLink from '@/components/Forms/PrimaryLink.vue'
import NotFoundResults from '@/components/Common/NotFoundResults.vue'
import TableSkeleton from '@/components/Common/Table/TableSkeleton.vue'
import CTALink from '@/components/Common/CTALink.vue'

import PlusIcon from '@heroicons/vue/24/outline/PlusIcon'
import MagnifyingGlassIcon from '@heroicons/vue/24/outline/MagnifyingGlassIcon'
import AdjustmentsHorizontalIcon from '@heroicons/vue/24/outline/AdjustmentsHorizontalIcon'

import { useHead } from 'unhead'

import { appTitle } from '@/global'

import { ref, onMounted, watch, computed } from 'vue'

import type TicketsFilters from '@/types/TicketsFilters'

import { onBeforeRouteUpdate, useRoute, useRouter } from 'vue-router'

import getTickets from '@/composables/tickets/getTickets'
import getCategories from '@/composables/categories/getCategories'

import debounce from 'lodash/debounce'

import EmptyTicketsLottie from '@/assets/emptyTickets.json'
import getTicketsCountsByStatus from '@/composables/tickets/getTicketsCountsByStatus'

useHead({ title: `Tickets | ${appTitle}` })

const route = useRoute()

const filters = ref({
  page: isNaN(Number(route.query.page)) ? null : Number(route.query.page),
  priority: route.query.priority ?? null,
  status: route.query.status ?? null,
  category: isNaN(Number(route.query.category)) ? null : Number(route.query.category),
  query: route.query.query ?? null,
  dates: route.query.from && route.query.to ? [route.query.from, route.query.to] : null
} as TicketsFilters)

const filtersAreApplied = computed(() => {
  return (
    filters.value.query ||
    filters.value.priority ||
    filters.value.status ||
    filters.value.category ||
    filters.value.dates ||
    filters.value.trash
  )
})

const { load, tickets, isLoading } = getTickets(filters)

const {
  load: loadTicketsCountsByStatus,
  ticketsCountsByStatus,
  isLoading: statsAreLoading
} = getTicketsCountsByStatus()

const router = useRouter()

const { load: fetchCategories, categories } = getCategories()

onMounted(async () => {
  await loadTicketsCountsByStatus()

  await load()

  await fetchCategories()
})

onBeforeRouteUpdate(async (to, from, next) => {
  filters.value = {
    page: isNaN(Number(to.query.page)) ? null : Number(to.query.page),
    priority: to.query.priority ?? null,
    status: to.query.status ?? null,
    category: isNaN(Number(to.query.category)) ? null : Number(to.query.category),
    query: to.query.query ?? null,
    dates: to.query.from && to.query.to ? [to.query.from, to.query.to] : null
  } as TicketsFilters

  await load()

  next()
})

const headers = ref(['Ticket', 'Status'])

const open = ref<boolean>(false)

const filter = async (newFilters: TicketsFilters) => {
  open.value = false

  isLoading.value = true

  filters.value = { ...newFilters, query: filters.value.query }

  const query: any = {}

  if (filters.value.query) query.query = filters.value.query
  if (newFilters.priority) query.priority = newFilters.priority
  if (newFilters.status) query.status = newFilters.status
  if (newFilters.category) query.category = newFilters.category
  if (newFilters.dates?.[0] && newFilters.dates?.[1]) {
    query.from = newFilters.dates[0]
    query.to = newFilters.dates[1]
  }

  if (
    !filters.value.query &&
    !newFilters.priority &&
    !newFilters.status &&
    !newFilters.category &&
    (!newFilters.dates?.[0] || !newFilters.dates?.[1])
  ) {
    isLoading.value = false
  }

  router.push({ query })
}

const query = ref(filters.value.query ?? '')

watch(
  query,
  debounce(async function (value: string) {
    isLoading.value = true

    filters.value.query = value

    const query: any = {}

    if (value) query.query = value
    if (filters.value.priority) query.priority = filters.value.priority
    if (filters.value.status) query.status = filters.value.status
    if (filters.value.category) query.category = filters.value.category
    if (filters.value.dates?.[0] && filters.value.dates?.[1]) {
      query.from = filters.value.dates[0]
      query.to = filters.value.dates[1]
    }

    router.push({ query })
  }, 300)
)

const reset = async () => {
  open.value = false

  if (
    filters.value.category ||
    filters.value.priority ||
    filters.value.status ||
    (filters.value.dates?.[0] && filters.value.dates?.[1])
  ) {
    isLoading.value = true

    const query: any = {}

    if (filters.value.query) {
      filters.value = { query: filters.value.query }
      query.query = filters.value.query
    } else {
      filters.value = {}
    }

    router.push({ query })
  }
}
</script>

<template>
  <section class="py-6 px-4 sm:p-6 lg:pb-8">
    <FilterTickets
      :filters="filters"
      :open="open"
      :temp-categories="categories"
      @close="open = false"
      @filter="filter"
      @reset="reset"
    />

    <SectionHeader title="Tickets" class="mb-6" />

    <div class="mb-6 grid grid-cols-2 gap-6 md:grid-cols-5">
      <template v-if="statsAreLoading">
        <StatSkeleton class="first:col-span-2 md:first:col-span-1" v-for="i in 5" :key="i" />
      </template>

      <MinimalStat
        class="first:col-span-2 md:first:col-span-1"
        v-for="item in ticketsCountsByStatus"
        :key="item.name"
        :item="item"
        v-else
      />
    </div>

    <header
      class="mb-3 flex flex-col justify-between gap-3 sm:flex-row-reverse"
      v-if="ticketsCountsByStatus?.[0]?.count"
    >
      <div class="flex justify-end sm:justify-start">
        <PrimaryLink to="ClientNewTicket" :Icon="PlusIcon" text="New Ticket" />
      </div>

      <div class="flex flex-1 gap-3">
        <div class="w-full max-w-sm">
          <label for="search" class="sr-only">Search</label>
          <div class="relative rounded-md shadow-sm">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
              <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
            </div>
            <input
              v-model="query"
              type="search"
              name="search"
              id="search"
              class="block w-full rounded-md border-gray-300 pl-10 text-sm focus:border-teal-500 focus:ring-teal-500"
              placeholder="Search"
            />
          </div>
        </div>

        <button
          @click="open = true"
          type="button"
          class="relative -ml-px inline-flex items-center space-x-2 rounded-md border border-gray-300 bg-gray-50 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500"
        >
          <AdjustmentsHorizontalIcon class="h-5 w-5 text-gray-400" />
          <span>Filter</span>
        </button>
      </div>
    </header>

    <TableSkeleton v-if="isLoading" />

    <NotFoundResults
      v-else-if="!isLoading && !tickets.data?.length && filtersAreApplied"
      text="No tickets related to your search"
    />

    <template v-else-if="ticketsCountsByStatus[0].count">
      <TableCard :headers="headers">
        <template v-slot>
          <tr v-for="ticket in tickets.data" :key="ticket.id">
            <TableTd>
              <router-link
                class="text-lg font-semibold text-teal-600 hover:text-teal-800"
                :to="{ name: 'ClientSingleTicket', params: { reference: ticket.reference } }"
              >
                {{ ticket.subject }}
              </router-link>

              <TicketInfo
                :reference="ticket.reference"
                :priority="ticket.priority"
                :created-at="ticket.created_at"
                :category-name="ticket.category.name"
                :last-reply-on="ticket.replies[0]?.created_at"
              />
            </TableTd>

            <TableTd>
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
            </TableTd>
          </tr>
        </template>

        <template v-slot:pagination>
          <Pagination
            class="border-t bg-gray-50 py-2 px-3"
            :from="tickets.from"
            :to="tickets.to"
            :total="tickets.total"
            :prev_page_url="tickets.prev_page_url"
            :next_page_url="tickets.next_page_url"
            :links="tickets.links"
            route-name="ClientTickets"
            :query="{
              query: $route.query.query,
              category: $route.query.category,
              priority: $route.query.priority,
              status: $route.query.status,
              from: $route.query.from,
              to: $route.query.to
            }"
          />
        </template>
      </TableCard>
    </template>

    <div v-else class="mb-12 border-gray-300 text-center">
      <LottieAnimation
        class="-mt-6 w-96 max-w-full"
        :animationData="EmptyTicketsLottie"
        :loop="false"
      />
      <h3 class="text-xl font-medium text-gray-900">No tickets</h3>
      <p class="mt-1 text-sm text-gray-500">Do you have any questions?</p>
      <div class="mt-6">
        <CTALink to="ClientNewTicket" text="Submit a ticket" />
      </div>
    </div>
  </section>
</template>
