<script setup lang="ts">
import TableCard from '@/components/Common/Table/TableCard.vue'
import TableTd from '@/components/Common/Table/TableTd.vue'
import Pagination from '@/components/Common/Pagination.vue'
import TableActions from '@/components/Common/Table/TableActions.vue'
import CannedResponseModal from '@/components/Manager/CannedResponses/CannedResponseModal.vue'
import FilterCannedResponses from '@/components/Manager/CannedResponses/FilterCannedResponses.vue'
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
import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

import getCannedResponses from '@/composables/canned-responses/getCannedResponses'
import useCannedResponses from '@/composables/canned-responses/useCannedResponses'
import getCategories from '@/composables/categories/getCategories'
import getUsers from '@/composables/users/getUsers'

import { ref, onMounted, watch, computed } from 'vue'

import type CannedResponse from '@/types/CannedResponse'
import type CannedResponsesFilters from '@/types/CannedResponsesFilters'
import type Option from '@/types/Option'
import type Category from '@/types/Category'
import type User from '@/types/User'

import debounce from 'lodash/debounce'

import { onBeforeRouteUpdate, useRoute, useRouter } from 'vue-router'

import { useToast } from 'vue-toastification'

useHead({ title: `Canned Responses | ${appTitle}` })

const authStore = useAuthStore()
const { isAdmin } = storeToRefs(authStore)

const { setTitle } = useDashboard()

setTitle('Canned Responses')

const route = useRoute()

const filters = ref({
  page: isNaN(Number(route.query.page)) ? null : Number(route.query.page),
  query: route.query.query ?? null,
  agent: isNaN(Number(route.query.agent)) ? null : Number(route.query.agent),
  category: isNaN(Number(route.query.category)) ? null : Number(route.query.category),
  trash: route.query.trash,
  paginate: true
} as CannedResponsesFilters)

const filtersAreApplied = computed(() => {
  return filters.value.query || filters.value.agent || filters.value.category || filters.value.trash
})

const { load, isLoading, paginatedCannedResponses: cannedResponses } = getCannedResponses(filters)

const handleAfterSave = async (keepQuery: boolean) => {
  if (!Object.keys(route.query).length) await load()
  else if (!keepQuery) router.push({ query: {} })
  else await load()
}

const categories = ref([] as Option[])
const agents = ref([] as Option[])

onMounted(async () => {
  await load()

  const { load: loadCategories, categories: tempCategories } = getCategories()
  await loadCategories()

  categories.value = tempCategories.value.map((category: Category) => ({
    name: category.name,
    value: category.id.toString()
  }))

  const usersFilters = ref({
    role: 'agent',
    paginate: false
  } as CannedResponsesFilters)
  const { load: loadAgents, users: tempAgents } = getUsers(usersFilters)

  if (isAdmin.value) {
    await loadAgents()

    agents.value = tempAgents.value.map((agent: User) => ({
      name: agent.name,
      value: agent.id.toString()
    }))
  }
})

const cannedResponseModalIsOpen = ref(false)

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
  cannedResponseToEdit.value = {} as CannedResponse

  cannedResponseModalIsOpen.value = true
}

const cannedResponseToEdit = ref({} as CannedResponse)

const handleEdit = (cannedResponse: CannedResponse) => {
  cannedResponseToEdit.value = cannedResponse

  cannedResponseModalIsOpen.value = true
}

const cannedResponseToDelete = ref({} as CannedResponse)

const handleDelete = (cannedResponse: CannedResponse) => {
  cannedResponseToDelete.value = cannedResponse

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Delete Canned Response'
  confirmationModalText.value = 'Are you sure you want to delete this canned response?'
  confirmationModalButtonText.value = 'Delete'
  confirmationModalBackgroundColor.value = 'bg-red-100'
  confirmationModalButtonBackgroundColor.value = 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
  confirmationModalColor.value = 'text-red-600'
  confirmationModalIcon.value = ExclamationTriangleIcon
  confirmationModalFunction.value = deleteCannedResponse
}

const handlePermanentDelete = (cannedResponse: CannedResponse) => {
  cannedResponseToDelete.value = cannedResponse

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Permanent Delete Canned Response'
  confirmationModalText.value =
    'Are you sure you want to permanently delete this canned response? This action cannot be undone.'
  confirmationModalButtonText.value = 'Delete'
  confirmationModalBackgroundColor.value = 'bg-red-100'
  confirmationModalButtonBackgroundColor.value = 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
  confirmationModalColor.value = 'text-red-600'
  confirmationModalIcon.value = ExclamationTriangleIcon
  confirmationModalFunction.value = forceDelete
}

const cannedResponseToRestore = ref({} as CannedResponse)

const handleRestore = (cannedResponse: CannedResponse) => {
  cannedResponseToRestore.value = cannedResponse

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Restore Canned Response'
  confirmationModalText.value = 'Are you sure you want to restore this canned response?'
  confirmationModalButtonText.value = 'Restore'
  confirmationModalBackgroundColor.value = 'bg-teal-100'
  confirmationModalButtonBackgroundColor.value = 'bg-teal-600 hover:bg-teal-700 focus:ring-teal-500'
  confirmationModalColor.value = 'text-teal-600'
  confirmationModalIcon.value = InformationCircleIcon
  confirmationModalFunction.value = restoreCannedResponse
}

const toast = useToast()

const { destroy, restore, isLoading: operationIsLoading, message, isSuccess } = useCannedResponses()

const deleteCannedResponse = async () => {
  await destroy(cannedResponseToDelete.value.id)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
  }
}

const forceDelete = async () => {
  await destroy(cannedResponseToDelete.value.id, true)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
  }
}

const restoreCannedResponse = async () => {
  await restore(cannedResponseToRestore.value.id)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
  }
}

const headers = ['Title', 'Category', 'Agent', 'Created At', '']

const router = useRouter()

const query = ref(filters.value.query ?? '')

watch(
  query,
  debounce(async function (value: string) {
    filters.value.query = value

    const query: any = {}

    if (value) query.query = value
    if (filters.value.trash) query.trash = filters.value.trash
    if (filters.value.agent) query.agent = filters.value.agent
    if (filters.value.category) query.category = filters.value.category

    router.push({ query })
  }, 300)
)

const filter = async (newFilters: CannedResponsesFilters) => {
  filterPanelIsOpen.value = false

  filters.value = { ...newFilters, query: filters.value.query, paginate: true }

  const query: any = {}

  query.trash = newFilters.trash
  if (filters.value.query) query.query = filters.value.query
  if (newFilters.agent) query.agent = newFilters.agent
  if (newFilters.category) query.category = newFilters.category

  router.push({ query })
}

const reset = async () => {
  filterPanelIsOpen.value = false

  if (filters.value.trash || filters.value.agent || filters.value.category) {
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
  if (to.query.page && to.query.page !== from.query.page) {
    filters.value = { ...filters.value, page: +to.query.page }
  }

  await load()
})
</script>

<template>
  <CannedResponseModal
    :open="cannedResponseModalIsOpen"
    :agents="agents"
    :categories="categories"
    :canned-response-to-edit="cannedResponseToEdit"
    @close="cannedResponseModalIsOpen = false"
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

  <FilterCannedResponses
    :agents="agents"
    :categories="categories"
    :filters="filters"
    :open="filterPanelIsOpen"
    @close="filterPanelIsOpen = false"
    @filter="filter"
    @reset="reset"
  />

  <header class="mb-3 flex flex-col justify-between gap-3 sm:flex-row-reverse">
    <div class="flex justify-end sm:justify-start">
      <ActionButton :Icon="PlusIcon" text="New Canned Response" :action="handleAdd" />
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
    v-else-if="!isLoading && !cannedResponses.data?.length && filtersAreApplied"
    text="No canned responses related to your search"
  />

  <TableCard :headers="headers" v-else>
    <template v-slot:default>
      <template v-if="cannedResponses.data?.length">
        <tr v-for="cannedResponse in cannedResponses.data" :key="cannedResponse.id">
          <TableTd>
            {{ cannedResponse.title }}
          </TableTd>

          <TableTd>
            {{ cannedResponse.category.name }}
          </TableTd>

          <TableTd>
            {{ cannedResponse.agent?.name ?? '--' }}
          </TableTd>

          <TableTd>
            {{ cannedResponse.created_at }}
          </TableTd>

          <TableTd>
            <TableActions>
              <template v-if="$route.query.trash != 'true'">
                <MenuItem class="cursor-pointer">
                  <div
                    @click="handleEdit(cannedResponse)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Edit
                  </div>
                </MenuItem>

                <MenuItem class="cursor-pointer">
                  <div
                    @click="handleDelete(cannedResponse)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Delete
                  </div>
                </MenuItem>
              </template>

              <template v-else>
                <MenuItem class="cursor-pointer">
                  <div
                    @click="handleRestore(cannedResponse)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Restore
                  </div>
                </MenuItem>

                <MenuItem class="cursor-pointer">
                  <div
                    @click="handlePermanentDelete(cannedResponse)"
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
            <p class="p-6 text-center text-xl">No canned responses yet</p>
          </TableTd>
        </tr>
      </template>
    </template>

    <template v-slot:pagination>
      <Pagination
        class="border-t bg-gray-50 py-2 px-3"
        :from="cannedResponses.from"
        :to="cannedResponses.to"
        :total="cannedResponses.total"
        :prev_page_url="cannedResponses.prev_page_url"
        :next_page_url="cannedResponses.next_page_url"
        :links="cannedResponses.links"
        route-name="DashboardCannedResponses"
        :query="{
          trash: $route.query.trash,
          query: $route.query.query,
          agent: $route.query.agent,
          category: $route.query.category
        }"
      />
    </template>
  </TableCard>
</template>
