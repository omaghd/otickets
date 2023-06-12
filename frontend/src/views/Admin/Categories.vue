<script setup lang="ts">
import TableCard from '@/components/Common/Table/TableCard.vue'
import TableTd from '@/components/Common/Table/TableTd.vue'
import Pagination from '@/components/Common/Pagination.vue'
import TableActions from '@/components/Common/Table/TableActions.vue'
import CategoryModal from '@/components/Admin/Categories/CategoryModal.vue'
import FilterCategories from '@/components/Admin/Categories/FilterCategories.vue'
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

import getCategories from '@/composables/categories/getCategories'
import getDepartments from '@/composables/departments/getDepartments'
import useCategories from '@/composables/categories/useCategories'

import { ref, onMounted, watch, computed } from 'vue'

import type Category from '@/types/Category'
import type CategoriesFilters from '@/types/CategoriesFilters'
import type Option from '@/types/Option'
import type Department from '@/types/Department'

import debounce from 'lodash/debounce'

import { onBeforeRouteUpdate, useRoute, useRouter } from 'vue-router'

import { useToast } from 'vue-toastification'

useHead({ title: `Categories | ${appTitle}` })

const { setTitle } = useDashboard()

setTitle('Categories')

const route = useRoute()

const filters = ref({
  page: isNaN(Number(route.query.page)) ? null : Number(route.query.page),
  query: route.query.query ?? null,
  department: isNaN(Number(route.query.department)) ? null : Number(route.query.department),
  trash: route.query.trash,
  paginate: true
} as CategoriesFilters)

const filtersAreApplied = computed(() => {
  return filters.value.query || filters.value.department || filters.value.trash
})

const { load, isLoading, paginatedCategories: categories } = getCategories(filters)

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

const categoryModalIsOpen = ref(false)

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
  categoryToEdit.value = {} as Category

  categoryModalIsOpen.value = true
}

const categoryToEdit = ref({} as Category)

const handleEdit = (category: Category) => {
  categoryToEdit.value = category

  categoryModalIsOpen.value = true
}

const categoryToDelete = ref({} as Category)

const handleDelete = (category: Category) => {
  categoryToDelete.value = category

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Delete Category'
  confirmationModalText.value = 'Are you sure you want to delete this category?'
  confirmationModalButtonText.value = 'Delete'
  confirmationModalBackgroundColor.value = 'bg-red-100'
  confirmationModalButtonBackgroundColor.value = 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
  confirmationModalColor.value = 'text-red-600'
  confirmationModalIcon.value = ExclamationTriangleIcon
  confirmationModalFunction.value = deleteCategory
}

const handlePermanentDelete = (category: Category) => {
  categoryToDelete.value = category

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Permanent Delete Category'
  confirmationModalText.value =
    'Are you sure you want to permanently delete this category? This action cannot be undone.'
  confirmationModalButtonText.value = 'Delete'
  confirmationModalBackgroundColor.value = 'bg-red-100'
  confirmationModalButtonBackgroundColor.value = 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
  confirmationModalColor.value = 'text-red-600'
  confirmationModalIcon.value = ExclamationTriangleIcon
  confirmationModalFunction.value = forceDelete
}

const categoryToRestore = ref({} as Category)

const handleRestore = (category: Category) => {
  categoryToRestore.value = category

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Restore Category'
  confirmationModalText.value = 'Are you sure you want to restore this category?'
  confirmationModalButtonText.value = 'Restore'
  confirmationModalBackgroundColor.value = 'bg-teal-100'
  confirmationModalButtonBackgroundColor.value = 'bg-teal-600 hover:bg-teal-700 focus:ring-teal-500'
  confirmationModalColor.value = 'text-teal-600'
  confirmationModalIcon.value = InformationCircleIcon
  confirmationModalFunction.value = restoreCategory
}

const toast = useToast()

const { destroy, restore, isLoading: operationIsLoading, message, isSuccess } = useCategories()

const deleteCategory = async () => {
  await destroy(categoryToDelete.value.id)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
  }
}

const forceDelete = async () => {
  await destroy(categoryToDelete.value.id, true)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
  }
}

const restoreCategory = async () => {
  await restore(categoryToRestore.value.id)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
  }
}

const headers = ['Category', 'Department', 'Tickets', 'Faqs', 'Canned Responses', 'Created At', '']

const router = useRouter()

const query = ref(filters.value.query ?? '')

watch(
  query,
  debounce(async function (value: string) {
    filters.value.query = value

    const query: any = {}

    if (value) query.query = value
    if (filters.value.trash) query.trash = filters.value.trash
    if (filters.value.department) query.department = filters.value.department

    router.push({ query })
  }, 300)
)

const filter = async (newFilters: CategoriesFilters) => {
  filterPanelIsOpen.value = false

  filters.value = { ...newFilters, query: filters.value.query, paginate: true }

  const query: any = {}

  query.trash = newFilters.trash
  if (filters.value.query) query.query = filters.value.query
  if (newFilters.department) query.department = newFilters.department

  if (!filters.value.query && !newFilters.trash && !newFilters.department) {
    isLoading.value = false
  }

  router.push({ query })
}

const reset = async () => {
  filterPanelIsOpen.value = false

  if (filters.value.trash || filters.value.department) {
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
  <CategoryModal
    :open="categoryModalIsOpen"
    :departments="departments"
    :category-to-edit="categoryToEdit"
    @close="categoryModalIsOpen = false"
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

  <FilterCategories
    :departments="departments"
    :filters="filters"
    :open="filterPanelIsOpen"
    @close="filterPanelIsOpen = false"
    @filter="filter"
    @reset="reset"
  />

  <header class="mb-3 flex flex-col justify-between gap-3 sm:flex-row-reverse">
    <div class="flex justify-end sm:justify-start">
      <ActionButton :Icon="PlusIcon" text="New Category" :action="handleAdd" />
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
    v-else-if="!isLoading && !categories.data?.length && filtersAreApplied"
    text="No categories related to your search"
  />

  <TableCard :headers="headers" v-else>
    <template v-slot:default>
      <template v-if="categories.data?.length">
        <tr v-for="category in categories.data" :key="category.id">
          <TableTd>
            <router-link
              v-tooltip.bottom="`View in a new tab`"
              :to="{ name: 'HomeCategory', params: { slug: category.slug } }"
              target="_blank"
            >
              {{ category.name }}
            </router-link>
          </TableTd>

          <TableTd>
            {{ category.department.name }}
          </TableTd>

          <TableTd>
            <router-link
              v-if="category.tickets_count"
              v-tooltip.bottom="`See ${category.name}'s tickets`"
              :to="{ name: 'DashboardTickets', query: { category: category.id } }"
            >
              {{ category.tickets_count }}
            </router-link>

            <span v-else>
              {{ category.tickets_count }}
            </span>
          </TableTd>

          <TableTd>
            <router-link
              v-if="category.faqs_count"
              v-tooltip.bottom="`See ${category.name}'s FAQs`"
              :to="{ name: 'DashboardFaqs', query: { category: category.id } }"
            >
              {{ category.faqs_count }}
            </router-link>

            <span v-else>
              {{ category.faqs_count }}
            </span>
          </TableTd>

          <TableTd>
            <router-link
              v-if="category.canned_responses_count"
              v-tooltip.bottom="`See ${category.name}'s canned responses`"
              :to="{ name: 'DashboardCannedResponses', query: { category: category.id } }"
            >
              {{ category.canned_responses_count }}
            </router-link>

            <span v-else>
              {{ category.canned_responses_count }}
            </span>
          </TableTd>

          <TableTd>
            {{ category.created_at }}
          </TableTd>

          <TableTd>
            <TableActions>
              <template v-if="$route.query.trash != 'true'">
                <MenuItem class="cursor-pointer">
                  <div
                    @click="handleEdit(category)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Edit
                  </div>
                </MenuItem>

                <MenuItem class="cursor-pointer">
                  <div
                    @click="handleDelete(category)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Delete
                  </div>
                </MenuItem>
              </template>

              <template v-else>
                <MenuItem class="cursor-pointer">
                  <div
                    @click="handleRestore(category)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Restore
                  </div>
                </MenuItem>

                <MenuItem class="cursor-pointer">
                  <div
                    @click="handlePermanentDelete(category)"
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
          <TableTd colspan="7">
            <p class="p-6 text-center text-xl">No categories yet</p>
          </TableTd>
        </tr>
      </template>
    </template>

    <template v-slot:pagination>
      <Pagination
        class="border-t bg-gray-50 py-2 px-3"
        :from="categories.from"
        :to="categories.to"
        :total="categories.total"
        :prev_page_url="categories.prev_page_url"
        :next_page_url="categories.next_page_url"
        :links="categories.links"
        route-name="DashboardCategories"
        :query="{
          query: $route.query.query,
          department: $route.query.department,
          trash: $route.query.trash
        }"
      />
    </template>
  </TableCard>
</template>
