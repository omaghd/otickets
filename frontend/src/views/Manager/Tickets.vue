<script setup lang="ts">
import TableCard from '@/components/Common/Table/TableCard.vue'
import TableTd from '@/components/Common/Table/TableTd.vue'
import Pagination from '@/components/Common/Pagination.vue'
import FilterTickets from '@/components/Common/Tickets/FilterTickets.vue'
import MinimalStat from '@/components/Common/MinimalStat.vue'
import StatSkeleton from '@/components/Common/StatSkeleton.vue'
import TicketInfo from '@/components/Common/Tickets/TicketInfo.vue'
import TableActions from '@/components/Common/Table/TableActions.vue'
import NewTicketModal from '@/components/Manager/Tickets/NewTicketModal.vue'
import ConfirmationModal from '@/components/Admin/ConfirmationModal.vue'
import useTickets from '@/composables/tickets/useTickets'
import ActionButton from '@/components/Common/ActionButton.vue'
import NotFoundResults from '@/components/Common/NotFoundResults.vue'
import TableSkeleton from '@/components/Common/Table/TableSkeleton.vue'
import EditTicketModal from '@/components/Manager/Tickets/EditTicketModal.vue'
import AssignAgentModal from '@/components/Manager/Tickets/AssignAgentModal.vue'

import { MenuItem } from '@headlessui/vue'

import PlusIcon from '@heroicons/vue/24/outline/PlusIcon'
import MagnifyingGlassIcon from '@heroicons/vue/24/outline/MagnifyingGlassIcon'
import AdjustmentsHorizontalIcon from '@heroicons/vue/24/outline/AdjustmentsHorizontalIcon'
import InformationCircleIcon from '@heroicons/vue/24/outline/InformationCircleIcon'
import ExclamationTriangleIcon from '@heroicons/vue/24/outline/ExclamationTriangleIcon'

import { ref, onMounted, watch, computed } from 'vue'

import type TicketsFilters from '@/types/TicketsFilters'
import type Ticket from '@/types/Ticket'
import type User from '@/types/User'

import { onBeforeRouteUpdate, useRoute, useRouter } from 'vue-router'

import getTickets from '@/composables/tickets/getTickets'
import getCategories from '@/composables/categories/getCategories'
import getTicketsCountsByStatus from '@/composables/tickets/getTicketsCountsByStatus'

import debounce from 'lodash/debounce'

import { useToast } from 'vue-toastification'

import { useDashboard } from '@/stores/dashboard'
import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

import { useHead } from 'unhead'

import { appTitle } from '@/global'

const { setTitle } = useDashboard()

setTitle('Tickets')

useHead({ title: `Tickets | ${appTitle}` })

const route = useRoute()

const filters = ref({
  page: isNaN(Number(route.query.page)) ? null : Number(route.query.page),
  trash: route.query.trash === 'true' ?? null,
  client: isNaN(Number(route.query.client)) ? null : Number(route.query.client),
  agent: isNaN(Number(route.query.agent)) ? null : Number(route.query.agent),
  priority: route.query.priority ?? null,
  status: route.query.status ?? null,
  category: isNaN(Number(route.query.category)) ? null : Number(route.query.category),
  query: route.query.query ?? null,
  dates: route.query.from && route.query.to ? [route.query.from, route.query.to] : null
} as TicketsFilters)

const filtersAreApplied = computed(() => {
  return (
    filters.value.trash ||
    filters.value.client ||
    filters.value.agent ||
    filters.value.priority ||
    filters.value.status ||
    filters.value.category ||
    filters.value.query ||
    filters.value.dates
  )
})

const { load, tickets, isLoading } = getTickets(filters)

const {
  load: fetchTicketsCountsByStatus,
  ticketsCountsByStatus,
  isLoading: statsAreLoading
} = getTicketsCountsByStatus()

const confirmationModalIsOpen = ref(false)
const confirmationModalTitle = ref('')
const confirmationModalText = ref('')
const confirmationModalButtonText = ref('')
const confirmationModalBackgroundColor = ref('')
const confirmationModalButtonBackgroundColor = ref('')
const confirmationModalColor = ref('')
const confirmationModalIcon = ref({} as any)
const confirmationModalFunction = ref({} as Function)

const { load: fetchCategories, categories } = getCategories()

onMounted(async () => {
  await fetchTicketsCountsByStatus()

  await load()

  await fetchCategories()
})

onBeforeRouteUpdate(async (to, from, next) => {
  filters.value = {
    page: isNaN(Number(to.query.page)) ? null : Number(to.query.page),
    trash: to.query.trash === 'true' ?? null,
    agent: to.query.agent ?? null,
    client: to.query.client ?? null,
    priority: to.query.priority ?? null,
    status: to.query.status ?? null,
    category: isNaN(Number(to.query.category)) ? null : Number(to.query.category),
    query: to.query.query ?? null,
    dates: to.query.from && to.query.to ? [to.query.from, to.query.to] : null
  } as TicketsFilters

  await load()

  next()
})

const userStore = useAuthStore()
const { user, isAdmin } = storeToRefs(userStore)

const API_URL = import.meta.env.VITE_API_URL

const headers = ['Ticket', 'Client', 'Current Agent', 'Status', '']

const open = ref(false)

const router = useRouter()

const filter = async (newFilters: TicketsFilters) => {
  open.value = false

  isLoading.value = true

  filters.value = { ...newFilters, query: filters.value.query }

  const query: any = {}

  if (filters.value.query) query.query = filters.value.query
  if (newFilters.trash) query.trash = newFilters.trash
  if (newFilters.client) query.client = newFilters.client
  if (newFilters.agent) query.agent = newFilters.agent
  if (newFilters.priority) query.priority = newFilters.priority
  if (newFilters.status) query.status = newFilters.status
  if (newFilters.category) query.category = newFilters.category
  if (newFilters.dates?.[0] && newFilters.dates?.[1]) {
    query.from = newFilters.dates[0]
    query.to = newFilters.dates[1]
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
    if (filters.value.trash) query.trash = filters.value.trash
    if (filters.value.agent) query.agent = filters.value.agent
    if (filters.value.client) query.client = filters.value.client
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
    filters.value.trash ||
    filters.value.agent ||
    filters.value.client ||
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

const {
  destroy,
  restore,
  assignAgent,
  message,
  isSuccess,
  isLoading: operationIsLoading
} = useTickets()

const handleAfterSave = async () => {
  await load()

  await fetchTicketsCountsByStatus()
}

const assignAgentModalIsOpen = ref(false)

const currentTicketAgents = ref([] as User[])

const handleAssignAgent = async (ticket: Ticket) => {
  ticketToEdit.value = ticket

  currentTicketAgents.value = ticket.agents

  assignAgentModalIsOpen.value = true
}

const handleAssignToMe = async (ticket: Ticket) => {
  if (!user.value.id) return

  await assignAgent(ticket.id, user.value.id, 'me')

  if (isSuccess.value) {
    await load()

    await fetchTicketsCountsByStatus()

    toast.success(message.value)
  } else {
    toast.error(message.value)
  }
}

const newTicketModalIsOpen = ref(false)

const handleAdd = () => {
  ticketToEdit.value = {} as Ticket

  newTicketModalIsOpen.value = true
}

const editTicketModalIsOpen = ref(false)

const ticketToEdit = ref({} as Ticket)

const handleEdit = (ticket: Ticket) => {
  ticketToEdit.value = ticket

  editTicketModalIsOpen.value = true
}

const ticketToDelete = ref({} as Ticket)

const handleDelete = (ticket: Ticket) => {
  ticketToDelete.value = ticket

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Delete Ticket'
  confirmationModalText.value = 'Are you sure you want to delete this ticket?'
  confirmationModalButtonText.value = 'Delete'
  confirmationModalBackgroundColor.value = 'bg-red-100'
  confirmationModalButtonBackgroundColor.value = 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
  confirmationModalColor.value = 'text-red-600'
  confirmationModalIcon.value = ExclamationTriangleIcon
  confirmationModalFunction.value = deleteTicket
}

const handlePermanentDelete = (ticket: Ticket) => {
  ticketToDelete.value = ticket

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Permanent Delete Ticket'
  confirmationModalText.value =
    'Are you sure you want to permanently delete this ticket? This action cannot be undone.'
  confirmationModalButtonText.value = 'Delete'
  confirmationModalBackgroundColor.value = 'bg-red-100'
  confirmationModalButtonBackgroundColor.value = 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
  confirmationModalColor.value = 'text-red-600'
  confirmationModalIcon.value = ExclamationTriangleIcon
  confirmationModalFunction.value = forceDelete
}

const ticketToRestore = ref({} as Ticket)

const handleRestore = (ticket: Ticket) => {
  ticketToRestore.value = ticket

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Restore Ticket'
  confirmationModalText.value = 'Are you sure you want to restore this ticket?'
  confirmationModalButtonText.value = 'Restore'
  confirmationModalBackgroundColor.value = 'bg-teal-100'
  confirmationModalButtonBackgroundColor.value = 'bg-teal-600 hover:bg-teal-700 focus:ring-teal-500'
  confirmationModalColor.value = 'text-teal-600'
  confirmationModalIcon.value = InformationCircleIcon
  confirmationModalFunction.value = restoreTicket
}

const toast = useToast()

const deleteTicket = async () => {
  await destroy(ticketToDelete.value.id)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
  }
}

const forceDelete = async () => {
  await destroy(ticketToDelete.value.id, true)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
  }
}

const restoreTicket = async () => {
  await restore(ticketToRestore.value.id)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
  }
}
</script>

<template>
  <NewTicketModal
    :open="newTicketModalIsOpen"
    :temp-categories="categories"
    @close="newTicketModalIsOpen = false"
    @success="handleAfterSave"
  />

  <EditTicketModal
    :open="editTicketModalIsOpen"
    :ticket-to-edit="ticketToEdit"
    :temp-categories="categories"
    @close="editTicketModalIsOpen = false"
    @success="handleAfterSave"
  />

  <AssignAgentModal
    :open="assignAgentModalIsOpen"
    :ticket-to-edit="ticketToEdit"
    :current-agents="currentTicketAgents"
    @close="assignAgentModalIsOpen = false"
    @success="handleAfterSave"
  />

  <ConfirmationModal
    :title="confirmationModalTitle"
    :open="confirmationModalIsOpen"
    :text="confirmationModalText"
    :button-text="confirmationModalButtonText"
    :is-loading="operationIsLoading"
    :Icon="confirmationModalIcon"
    :color="confirmationModalColor"
    :background-color="confirmationModalBackgroundColor"
    @close="confirmationModalIsOpen = false"
    @confirm="confirmationModalFunction()"
    :button-background-color="confirmationModalButtonBackgroundColor"
  />

  <FilterTickets
    :filters="filters"
    :open="open"
    :temp-categories="categories"
    @close="open = false"
    @filter="filter"
    @reset="reset"
  />

  <div class="mb-6 grid grid-cols-2 gap-6 xl:grid-cols-5">
    <template v-if="statsAreLoading">
      <StatSkeleton class="first:col-span-2 xl:first:col-span-1" v-for="i in 5" :key="i" />
    </template>

    <MinimalStat
      class="first:col-span-2 xl:first:col-span-1"
      v-for="item in ticketsCountsByStatus"
      :key="item.name"
      :item="item"
      v-else
    />
  </div>

  <header class="mb-3 flex flex-col justify-between gap-3 sm:flex-row-reverse">
    <div class="flex justify-end sm:justify-start">
      <ActionButton :Icon="PlusIcon" text="New Ticket" :action="handleAdd" />
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

  <TableCard :headers="headers" v-else>
    <template v-slot>
      <template v-if="tickets.data?.length">
        <tr v-for="ticket in tickets.data" :key="ticket.id">
          <TableTd>
            <component
              :is="
                isAdmin || ticket.agents.filter((agent) => agent.id === user.id).length
                  ? 'router-link'
                  : 'span'
              "
              :class="{ 'hover:text-teal-800': isAdmin || ticket.agents?.[0]?.id === user.id }"
              class="whitespace-normal text-lg font-semibold text-teal-600"
              :to="{ name: 'DashboardSingleTicket', params: { reference: ticket.reference } }"
            >
              {{ ticket.subject }}
            </component>

            <TicketInfo
              :reference="ticket.reference"
              :priority="ticket.priority"
              :created-at="ticket.created_at"
              :category-name="ticket.category.name"
              :last-reply-on="ticket.replies[0]?.created_at"
            />
          </TableTd>

          <TableTd>
            <div class="flex items-center gap-2">
              <div class="h-10 w-10 flex-shrink-0">
                <img
                  class="h-10 w-10 rounded-full"
                  :src="API_URL + ticket.client.picture"
                  alt="Profile Picture"
                />
              </div>
              <div>
                <div class="font-medium text-gray-900">{{ ticket.client.name }}</div>
                <div class="text-xs text-gray-500">{{ ticket.client.email }}</div>
              </div>
            </div>
          </TableTd>

          <TableTd>
            <div
              class="flex items-center gap-2"
              v-tooltip.bottom="`Assigned on ${ticket.agents?.[0]?.pivot.created_at}`"
              v-if="ticket.agents?.[0]?.id"
            >
              <div class="h-10 w-10 flex-shrink-0">
                <img
                  class="h-10 w-10 rounded-full"
                  :src="API_URL + ticket.agents[0]?.picture"
                  alt="Profile Picture"
                />
              </div>
              <div>
                <div class="font-medium text-gray-900">{{ ticket.agents[0].name }}</div>
                <div class="text-xs font-medium text-gray-500">
                  {{ ticket.agents[0].department.name }}
                </div>
                <div class="text-xs text-gray-500">{{ ticket.agents[0].email }}</div>
              </div>
            </div>

            <span v-else>--</span>
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

          <TableTd>
            <TableActions
              v-if="
                isAdmin ||
                (ticket.status !== 'resolved' &&
                  ticket.status !== 'closed' &&
                  (ticket.agents?.[0]?.id === user.id || !ticket.agents.length))
              "
            >
              <template v-if="$route.query.trash != 'true'">
                <MenuItem class="cursor-pointer" v-if="isAdmin || ticket.agents.length">
                  <div
                    @click="handleAssignAgent(ticket)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Assign Agent
                  </div>
                </MenuItem>

                <MenuItem class="cursor-pointer" v-if="!isAdmin && !ticket.agents.length">
                  <div
                    @click="handleAssignToMe(ticket)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Assign to me
                  </div>
                </MenuItem>

                <MenuItem
                  class="cursor-pointer"
                  v-if="isAdmin || ticket.agents?.[0]?.id === user.id"
                >
                  <div
                    @click="handleEdit(ticket)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Edit
                  </div>
                </MenuItem>

                <MenuItem class="cursor-pointer" v-if="isAdmin">
                  <div
                    @click="handleDelete(ticket)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Delete
                  </div>
                </MenuItem>
              </template>

              <template v-else-if="isAdmin">
                <MenuItem class="cursor-pointer">
                  <div
                    @click="handleRestore(ticket)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Restore
                  </div>
                </MenuItem>

                <MenuItem class="cursor-pointer">
                  <div
                    @click="handlePermanentDelete(ticket)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Delete permanently
                  </div>
                </MenuItem>
              </template>
            </TableActions>
          </TableTd>
        </tr>
      </template>

      <template v-else>
        <tr>
          <TableTd colspan="5">
            <p class="p-6 text-center text-xl">No tickets yet</p>
          </TableTd>
        </tr>
      </template>
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
          trash: $route.query.trash,
          client: $route.query.client,
          agent: $route.query.agent,
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
