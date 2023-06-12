<script setup lang="ts">
import TableCard from '@/components/Common/Table/TableCard.vue'
import TableTd from '@/components/Common/Table/TableTd.vue'
import Pagination from '@/components/Common/Pagination.vue'
import TableActions from '@/components/Common/Table/TableActions.vue'
import ClientModal from '@/components/Admin/Clients/ClientModal.vue'
import FilterClients from '@/components/Admin/Clients/FilterClients.vue'
import ConfirmationModal from '@/components/Admin/ConfirmationModal.vue'
import TableSkeleton from '@/components/Common/Table/TableSkeleton.vue'
import ActionButton from '@/components/Common/ActionButton.vue'
import NotFoundResults from '@/components/Common/NotFoundResults.vue'

import { MenuItem } from '@headlessui/vue'

import MagnifyingGlassIcon from '@heroicons/vue/24/outline/MagnifyingGlassIcon'
import AdjustmentsHorizontalIcon from '@heroicons/vue/24/outline/AdjustmentsHorizontalIcon'
import PlusIcon from '@heroicons/vue/24/outline/PlusIcon'
import ExclamationTriangleIcon from '@heroicons/vue/24/outline/ExclamationTriangleIcon'
import InformationCircleIcon from '@heroicons/vue/24/outline/InformationCircleIcon'

import { useHead } from 'unhead'

import { appTitle } from '@/global'

import { useDashboard } from '@/stores/dashboard'

import getClients from '@/composables/clients/getClients'
import getDepartments from '@/composables/departments/getDepartments'
import useClients from '@/composables/clients/useClients'

import { ref, onMounted, watch, computed } from 'vue'

import type Client from '@/types/Client'
import type ClientsFilters from '@/types/ClientsFilters'
import type Option from '@/types/Option'
import type Department from '@/types/Department'

import debounce from 'lodash/debounce'

import { onBeforeRouteUpdate, useRoute, useRouter } from 'vue-router'

import { useToast } from 'vue-toastification'

useHead({ title: `Clients | ${appTitle}` })

const { setTitle } = useDashboard()

setTitle('Clients')

const route = useRoute()

const filters = ref({
  page: isNaN(Number(route.query.page)) ? null : Number(route.query.page),
  query: route.query.query ?? null,
  trash: route.query.trash,
  dates: route.query.from && route.query.to ? [route.query.from, route.query.to] : null,
  paginate: true
} as ClientsFilters)

const filtersAreApplied = computed(() => {
  return filters.value.query || filters.value.trash || filters.value.dates
})

const { load, isLoading, paginatedClients: clients } = getClients(filters)

const handleAfterSave = async (keepQuery: boolean) => {
  if (!Object.keys(route.query).length) await load()
  else if (!keepQuery) router.push({ query: {} })
  else await load()
}

const departments = ref([] as Option[])

onMounted(async () => {
  await load()

  const { load: loadDepartments, departments: tempDepartments } = getDepartments()
  await loadDepartments()

  departments.value = tempDepartments.value.map((department: Department) => ({
    name: department.name,
    value: department.id.toString()
  }))
})

const clientModalIsOpen = ref(false)

const confirmationModalIsOpen = ref(false)
const confirmationModalTitle = ref('')
const confirmationModalText = ref('')
const confirmationModalButtonText = ref('')
const confirmationModalBackgroundColor = ref('')
const confirmationModalButtonBackgroundColor = ref('')
const confirmationModalColor = ref('')
const confirmationModalIcon = ref({} as any)
const confirmationModalFunction = ref({} as Function)

const filterPanelIsOpen = ref(false)

const handleAdd = () => {
  clientToEdit.value = {} as Client

  clientModalIsOpen.value = true
}

const clientToEdit = ref({} as Client)

const handleEdit = (client: Client) => {
  clientToEdit.value = client

  clientModalIsOpen.value = true
}

const clientToDelete = ref({} as Client)

const handleDelete = (client: Client) => {
  clientToDelete.value = client

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Delete Client'
  confirmationModalText.value = 'Are you sure you want to delete this client?'
  confirmationModalButtonText.value = 'Delete'
  confirmationModalBackgroundColor.value = 'bg-red-100'
  confirmationModalButtonBackgroundColor.value = 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
  confirmationModalColor.value = 'text-red-600'
  confirmationModalIcon.value = ExclamationTriangleIcon
  confirmationModalFunction.value = deleteClient
}

const handlePermanentDelete = (client: Client) => {
  clientToDelete.value = client

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Permanent Delete Client'
  confirmationModalText.value =
    'Are you sure you want to permanently delete this client? This action cannot be undone.'
  confirmationModalButtonText.value = 'Delete'
  confirmationModalBackgroundColor.value = 'bg-red-100'
  confirmationModalButtonBackgroundColor.value = 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
  confirmationModalColor.value = 'text-red-600'
  confirmationModalIcon.value = ExclamationTriangleIcon
  confirmationModalFunction.value = forceDelete
}

const clientToRestore = ref({} as Client)

const handleRestore = (client: Client) => {
  clientToRestore.value = client

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Restore Client'
  confirmationModalText.value = 'Are you sure you want to restore this client?'
  confirmationModalButtonText.value = 'Restore'
  confirmationModalBackgroundColor.value = 'bg-teal-100'
  confirmationModalButtonBackgroundColor.value = 'bg-teal-600 hover:bg-teal-700 focus:ring-teal-500'
  confirmationModalColor.value = 'text-teal-600'
  confirmationModalIcon.value = InformationCircleIcon
  confirmationModalFunction.value = restoreClient
}

const toast = useToast()

const { destroy, restore, isLoading: operationIsLoading, message, isSuccess } = useClients()

const deleteClient = async () => {
  await destroy(clientToDelete.value.id)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
  }
}

const forceDelete = async () => {
  await destroy(clientToDelete.value.id, true)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
  }
}

const restoreClient = async () => {
  await restore(clientToRestore.value.id)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
  }
}

const headers = ['Client', 'Tickets', 'Created At', '']

const API_URL = import.meta.env.VITE_API_URL

const router = useRouter()

const query = ref(filters.value.query ?? '')

watch(
  query,
  debounce(async function (value: string) {
    filters.value.query = value

    const query: any = {}

    if (value) query.query = value
    if (filters.value.trash) query.trash = filters.value.trash
    if (filters.value.dates?.[0] && filters.value.dates?.[1]) {
      query.from = filters.value.dates[0]
      query.to = filters.value.dates[1]
    }

    router.push({ query })
  }, 300)
)

const filter = async (newFilters: ClientsFilters) => {
  filterPanelIsOpen.value = false

  filters.value = { ...newFilters, query: filters.value.query, paginate: true }

  const query: any = {}

  query.trash = newFilters.trash
  if (filters.value.query) query.query = filters.value.query
  if (newFilters.dates?.[0] && newFilters.dates?.[1]) {
    query.from = newFilters.dates[0]
    query.to = newFilters.dates[1]
  }

  router.push({ query })
}

const reset = async () => {
  filterPanelIsOpen.value = false

  if (filters.value.trash || (filters.value.dates?.[0] && filters.value.dates?.[1])) {
    const query: any = {}

    if (filters.value.query) {
      filters.value = { query: filters.value.query, paginate: true }
      query.query = filters.value.query
    } else {
      filters.value = { paginate: true }
    }

    router.push({ query })
  }
}

onBeforeRouteUpdate(async (to, from) => {
  if (to.query.page !== from.query.page) {
    filters.value = { ...filters.value, page: Number(to.query.page) }
  }

  await load()
})
</script>

<template>
  <ClientModal
    :open="clientModalIsOpen"
    :departments="departments"
    :client-to-edit="clientToEdit"
    @close="clientModalIsOpen = false"
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

  <FilterClients
    :departments="departments"
    :filters="filters"
    :open="filterPanelIsOpen"
    @close="filterPanelIsOpen = false"
    @filter="filter"
    @reset="reset"
  />

  <header class="mb-3 flex flex-col justify-between gap-3 sm:flex-row-reverse">
    <div class="flex justify-end sm:justify-start">
      <ActionButton :Icon="PlusIcon" text="New Client" :action="handleAdd" />
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
        @click="filterPanelIsOpen = true"
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
    v-else-if="!isLoading && !clients.data?.length && filtersAreApplied"
    text="No clients related to your search"
  />

  <TableCard :headers="headers" v-else>
    <template v-slot:default>
      <template v-if="clients.data?.length">
        <tr v-for="client in clients.data" :key="client.id">
          <TableTd>
            <div class="flex items-center gap-2">
              <div class="h-10 w-10 flex-shrink-0">
                <img
                  class="h-10 w-10 rounded-full"
                  :src="API_URL + client.picture"
                  alt="Profile Picture"
                />
              </div>
              <div>
                <div class="font-medium text-gray-900">{{ client.name }}</div>
                <div class="text-gray-500">{{ client.email }}</div>
              </div>
            </div>
          </TableTd>

          <TableTd>
            <router-link
              v-tooltip.bottom="`See ${client.name}'s tickets`"
              :to="{ name: 'DashboardTickets', query: { client: client.id } }"
            >
              {{ client.tickets_count }}
            </router-link>
          </TableTd>

          <TableTd>
            {{ client.created_at }}
          </TableTd>

          <TableTd>
            <TableActions>
              <template v-if="$route.query.trash != 'true'">
                <MenuItem class="cursor-pointer">
                  <div
                    @click="handleEdit(client)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Edit
                  </div>
                </MenuItem>

                <MenuItem class="cursor-pointer">
                  <div
                    @click="handleDelete(client)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Delete
                  </div>
                </MenuItem>
              </template>

              <template v-else>
                <MenuItem class="cursor-pointer">
                  <div
                    @click="handleRestore(client)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Restore
                  </div>
                </MenuItem>

                <MenuItem class="cursor-pointer">
                  <div
                    @click="handlePermanentDelete(client)"
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
            <p class="p-6 text-center text-xl">No clients yet</p>
          </TableTd>
        </tr>
      </template>
    </template>

    <template v-slot:pagination>
      <Pagination
        class="border-t bg-gray-50 py-2 px-3"
        :from="clients.from"
        :to="clients.to"
        :total="clients.total"
        :prev_page_url="clients.prev_page_url"
        :next_page_url="clients.next_page_url"
        :links="clients.links"
        route-name="DashboardClients"
        :query="{
          trash: $route.query.trash,
          query: $route.query.query,
          department: $route.query.department,
          role: $route.query.role,
          from: $route.query.from,
          to: $route.query.to
        }"
      />
    </template>
  </TableCard>
</template>
