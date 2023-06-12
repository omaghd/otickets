<script setup lang="ts">
import TableCard from '@/components/Common/Table/TableCard.vue'
import TableTd from '@/components/Common/Table/TableTd.vue'
import Pagination from '@/components/Common/Pagination.vue'
import TableActions from '@/components/Common/Table/TableActions.vue'
import FaqModal from '@/components/Admin/Faqs/FaqModal.vue'
import FilterFaqs from '@/components/Admin/Faqs/FilterFaqs.vue'
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

import getFaqs from '@/composables/faqs/getFaqs'
import getCategories from '@/composables/categories/getCategories'
import useFaqs from '@/composables/faqs/useFaqs'

import { ref, onMounted, watch, computed } from 'vue'

import type Faq from '@/types/Faq'
import type FaqsFilters from '@/types/FaqsFilters'
import type Option from '@/types/Option'
import type Category from '@/types/Category'

import debounce from 'lodash/debounce'

import { onBeforeRouteUpdate, useRoute, useRouter } from 'vue-router'

import { useToast } from 'vue-toastification'

useHead({ title: `FAQs | ${appTitle}` })

const { setTitle } = useDashboard()

setTitle('FAQs')

const route = useRoute()

const filters = ref({
  page: isNaN(Number(route.query.page)) ? null : Number(route.query.page),
  query: route.query.query ?? null,
  category: isNaN(Number(route.query.category)) ? null : Number(route.query.category),
  trash: route.query.trash,
  dates: route.query.from && route.query.to ? [route.query.from, route.query.to] : null
} as FaqsFilters)

const filtersAreApplied = computed(() => {
  return filters.value.query || filters.value.category || filters.value.trash || filters.value.dates
})

const { load, isLoading, faqs } = getFaqs(filters)

const handleAfterSave = async (keepQuery: boolean) => {
  if (!Object.keys(route.query).length) await load()
  else if (!keepQuery) router.push({ query: {} })
  else await load()
}

const categories = ref([] as Option[])

onMounted(async () => {
  await load()

  const { load: loadCategories, categories: tempCategories } = getCategories()
  await loadCategories()

  categories.value = tempCategories.value.map((category: Category) => ({
    name: category.name,
    value: category.id.toString()
  }))
})

const faqModalIsOpen = ref(false)

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
  faqToEdit.value = {} as Faq

  faqModalIsOpen.value = true
}

const faqToEdit = ref({} as Faq)

const handleEdit = (faq: Faq) => {
  faqToEdit.value = faq

  faqModalIsOpen.value = true
}

const faqToDelete = ref({} as Faq)

const handleDelete = (faq: Faq) => {
  faqToDelete.value = faq

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Delete Faq'
  confirmationModalText.value = 'Are you sure you want to delete this faq?'
  confirmationModalButtonText.value = 'Delete'
  confirmationModalBackgroundColor.value = 'bg-red-100'
  confirmationModalButtonBackgroundColor.value = 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
  confirmationModalColor.value = 'text-red-600'
  confirmationModalIcon.value = ExclamationTriangleIcon
  confirmationModalFunction.value = deleteFaq
}

const handlePermanentDelete = (faq: Faq) => {
  faqToDelete.value = faq

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Permanent Delete Faq'
  confirmationModalText.value =
    'Are you sure you want to permanently delete this faq? This action cannot be undone.'
  confirmationModalButtonText.value = 'Delete'
  confirmationModalBackgroundColor.value = 'bg-red-100'
  confirmationModalButtonBackgroundColor.value = 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
  confirmationModalColor.value = 'text-red-600'
  confirmationModalIcon.value = ExclamationTriangleIcon
  confirmationModalFunction.value = forceDelete
}

const faqToRestore = ref({} as Faq)

const handleRestore = (faq: Faq) => {
  faqToRestore.value = faq

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Restore Faq'
  confirmationModalText.value = 'Are you sure you want to restore this faq?'
  confirmationModalButtonText.value = 'Restore'
  confirmationModalBackgroundColor.value = 'bg-teal-100'
  confirmationModalButtonBackgroundColor.value = 'bg-teal-600 hover:bg-teal-700 focus:ring-teal-500'
  confirmationModalColor.value = 'text-teal-600'
  confirmationModalIcon.value = InformationCircleIcon
  confirmationModalFunction.value = restoreFaq
}

const toast = useToast()

const { destroy, restore, isLoading: operationIsLoading, message, isSuccess } = useFaqs()

const deleteFaq = async () => {
  await destroy(faqToDelete.value.id)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
  }
}

const forceDelete = async () => {
  await destroy(faqToDelete.value.id, true)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
  }
}

const restoreFaq = async () => {
  await restore(faqToRestore.value.id)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
  }
}

const headers = ['Question', 'Category', 'Status', 'Created At', 'Updated At', '']

const router = useRouter()

const query = ref(filters.value.query ?? '')

watch(
  query,
  debounce(async function (value: string) {
    filters.value.query = value

    const query: any = {}

    if (value) query.query = value
    if (filters.value.trash) query.trash = filters.value.trash
    if (filters.value.category) query.category = filters.value.category
    if (filters.value.dates?.[0] && filters.value.dates?.[1]) {
      query.from = filters.value.dates[0]
      query.to = filters.value.dates[1]
    }

    router.push({ query })
  }, 300)
)

const filter = async (newFilters: FaqsFilters) => {
  filterPanelIsOpen.value = false

  filters.value = { ...newFilters, query: filters.value.query }

  const query: any = {}

  query.trash = newFilters.trash
  if (filters.value.query) query.query = filters.value.query
  if (newFilters.category) query.category = newFilters.category
  if (newFilters.dates?.[0] && newFilters.dates?.[1]) {
    query.from = newFilters.dates[0]
    query.to = newFilters.dates[1]
  }

  router.push({ query })
}

const reset = async () => {
  filterPanelIsOpen.value = false

  if (
    filters.value.trash ||
    filters.value.category ||
    (filters.value.dates?.[0] && filters.value.dates?.[1])
  ) {
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

onBeforeRouteUpdate(async (to, from) => {
  if (to.query.page !== from.query.page) {
    filters.value = { ...filters.value, page: Number(to.query.page) }
  }

  await load()
})
</script>

<template>
  <FaqModal
    :open="faqModalIsOpen"
    :categories="categories"
    :faq-to-edit="faqToEdit"
    @close="faqModalIsOpen = false"
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

  <FilterFaqs
    :categories="categories"
    :filters="filters"
    :open="filterPanelIsOpen"
    @close="filterPanelIsOpen = false"
    @filter="filter"
    @reset="reset"
  />

  <header class="mb-3 flex flex-col justify-between gap-3 sm:flex-row-reverse">
    <div class="flex justify-end sm:justify-start">
      <ActionButton :Icon="PlusIcon" text="New FAQ" :action="handleAdd" />
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
    v-else-if="!isLoading && !faqs.data?.length && filtersAreApplied"
    text="No faqs related to your search"
  />

  <TableCard :headers="headers" v-else>
    <template v-slot:default>
      <template v-if="faqs.data?.length">
        <tr v-for="faq in faqs.data" :key="faq.id">
          <TableTd>
            <router-link
              v-tooltip.bottom="`View in a new tab`"
              :to="{ name: 'SingleFaq', params: { slug: faq.slug } }"
              target="_blank"
            >
              {{ faq.question }}
            </router-link>
          </TableTd>

          <TableTd>
            {{ faq.category.name }}
          </TableTd>

          <TableTd>
            <span
              :class="
                faq.is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
              "
              class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
            >
              {{ faq.is_published ? 'Published' : 'Draft' }}
            </span>
          </TableTd>

          <TableTd>
            {{ faq.created_at }}
          </TableTd>

          <TableTd>
            {{ faq.updated_at }}
          </TableTd>

          <TableTd>
            <TableActions>
              <template v-if="$route.query.trash != 'true'">
                <MenuItem class="cursor-pointer">
                  <div
                    @click="handleEdit(faq)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Edit
                  </div>
                </MenuItem>

                <MenuItem class="cursor-pointer">
                  <div
                    @click="handleDelete(faq)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Delete
                  </div>
                </MenuItem>
              </template>

              <template v-else>
                <MenuItem class="cursor-pointer">
                  <div
                    @click="handleRestore(faq)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Restore
                  </div>
                </MenuItem>

                <MenuItem class="cursor-pointer">
                  <div
                    @click="handlePermanentDelete(faq)"
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
          <TableTd colspan="6">
            <p class="p-6 text-center text-xl">No faqs yet</p>
          </TableTd>
        </tr>
      </template>
    </template>

    <template v-slot:pagination>
      <Pagination
        class="border-t bg-gray-50 py-2 px-3"
        :from="faqs.from"
        :to="faqs.to"
        :total="faqs.total"
        :prev_page_url="faqs.prev_page_url"
        :next_page_url="faqs.next_page_url"
        :links="faqs.links"
        route-name="DashboardFaqs"
        :query="{
          trash: $route.query.trash,
          query: $route.query.query,
          category: $route.query.category,
          from: $route.query.from,
          to: $route.query.to
        }"
      />
    </template>
  </TableCard>
</template>
