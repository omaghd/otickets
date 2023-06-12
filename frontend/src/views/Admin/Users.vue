<script setup lang="ts">
import TableCard from '@/components/Common/Table/TableCard.vue'
import TableTd from '@/components/Common/Table/TableTd.vue'
import Pagination from '@/components/Common/Pagination.vue'
import TableActions from '@/components/Common/Table/TableActions.vue'
import UserModal from '@/components/Admin/Users/UserModal.vue'
import FilterUsers from '@/components/Admin/Users/FilterUsers.vue'
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

import getUsers from '@/composables/users/getUsers'
import getDepartments from '@/composables/departments/getDepartments'
import useUsers from '@/composables/users/useUsers'

import { ref, onMounted, watch, computed } from 'vue'

import type User from '@/types/User'
import type UsersFilters from '@/types/UsersFilters'
import type Option from '@/types/Option'
import type Department from '@/types/Department'

import debounce from 'lodash/debounce'

import { onBeforeRouteUpdate, useRoute, useRouter } from 'vue-router'

import { useToast } from 'vue-toastification'

useHead({ title: `Users | ${appTitle}` })

const { setTitle } = useDashboard()

setTitle('Users')

const route = useRoute()

const filters = ref({
  page: isNaN(Number(route.query.page)) ? null : Number(route.query.page),
  query: route.query.query ?? null,
  department: isNaN(Number(route.query.department)) ? null : Number(route.query.department),
  trash: route.query.trash,
  role: route.query.role ?? null,
  dates: route.query.from && route.query.to ? [route.query.from, route.query.to] : null,
  paginate: true
} as UsersFilters)

const filtersAreApplied = computed(() => {
  return (
    filters.value.query ||
    filters.value.department ||
    filters.value.role ||
    filters.value.dates ||
    filters.value.trash
  )
})

const { load, isLoading, paginatedUsers: users } = getUsers(filters)

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

const userModalIsOpen = ref(false)

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
  userToEdit.value = {} as User

  userModalIsOpen.value = true
}

const userToEdit = ref({} as User)

const handleEdit = (user: User) => {
  userToEdit.value = user

  userModalIsOpen.value = true
}

const userToDelete = ref({} as User)

const handleDelete = (user: User) => {
  userToDelete.value = user

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Delete User'
  confirmationModalText.value = 'Are you sure you want to delete this user?'
  confirmationModalButtonText.value = 'Delete'
  confirmationModalBackgroundColor.value = 'bg-red-100'
  confirmationModalButtonBackgroundColor.value = 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
  confirmationModalColor.value = 'text-red-600'
  confirmationModalIcon.value = ExclamationTriangleIcon
  confirmationModalFunction.value = deleteUser
}

const handlePermanentDelete = (user: User) => {
  userToDelete.value = user

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Permanent Delete User'
  confirmationModalText.value =
    'Are you sure you want to permanently delete this user? This action cannot be undone.'
  confirmationModalButtonText.value = 'Delete'
  confirmationModalBackgroundColor.value = 'bg-red-100'
  confirmationModalButtonBackgroundColor.value = 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
  confirmationModalColor.value = 'text-red-600'
  confirmationModalIcon.value = ExclamationTriangleIcon
  confirmationModalFunction.value = forceDelete
}

const userToRestore = ref({} as User)

const handleRestore = (user: User) => {
  userToRestore.value = user

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Restore User'
  confirmationModalText.value = 'Are you sure you want to restore this user?'
  confirmationModalButtonText.value = 'Restore'
  confirmationModalBackgroundColor.value = 'bg-teal-100'
  confirmationModalButtonBackgroundColor.value = 'bg-teal-600 hover:bg-teal-700 focus:ring-teal-500'
  confirmationModalColor.value = 'text-teal-600'
  confirmationModalIcon.value = InformationCircleIcon
  confirmationModalFunction.value = restoreUser
}

const toast = useToast()

const { destroy, restore, isLoading: operationIsLoading, message, isSuccess } = useUsers()

const deleteUser = async () => {
  await destroy(userToDelete.value.id)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
  }
}

const forceDelete = async () => {
  await destroy(userToDelete.value.id, true)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
  }
}

const restoreUser = async () => {
  await restore(userToRestore.value.id)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
  }
}

const headers = ['User', 'Role', 'Department', 'Created At', '']

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
    if (filters.value.department) query.department = filters.value.department
    if (filters.value.role) query.role = filters.value.role
    if (filters.value.dates?.[0] && filters.value.dates?.[1]) {
      query.from = filters.value.dates[0]
      query.to = filters.value.dates[1]
    }

    router.push({ query })
  }, 300)
)

const filter = async (newFilters: UsersFilters) => {
  filterPanelIsOpen.value = false

  filters.value = { ...newFilters, query: filters.value.query, paginate: true }

  const query: any = {}

  query.trash = newFilters.trash
  if (filters.value.query) query.query = filters.value.query
  if (newFilters.role) query.role = newFilters.role
  if (newFilters.department) query.department = newFilters.department
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
    filters.value.role ||
    filters.value.department ||
    (filters.value.dates?.[0] && filters.value.dates?.[1])
  ) {
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
  <UserModal
    :open="userModalIsOpen"
    :departments="departments"
    :user-to-edit="userToEdit"
    @close="userModalIsOpen = false"
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

  <FilterUsers
    :departments="departments"
    :filters="filters"
    :open="filterPanelIsOpen"
    @close="filterPanelIsOpen = false"
    @filter="filter"
    @reset="reset"
  />

  <header class="mb-3 flex flex-col justify-between gap-3 sm:flex-row-reverse">
    <div class="flex justify-end sm:justify-start">
      <ActionButton :Icon="PlusIcon" text="New User" :action="handleAdd" />
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
    v-else-if="!isLoading && !users.data?.length && filtersAreApplied"
    text="No users related to your search"
  />

  <TableCard :headers="headers" v-else>
    <template v-slot:default>
      <template v-if="users.data?.length">
        <tr v-for="user in users.data" :key="user.id">
          <TableTd>
            <div class="flex items-center gap-2">
              <div class="h-10 w-10 flex-shrink-0">
                <img
                  class="h-10 w-10 rounded-full"
                  :src="API_URL + user.picture"
                  alt="Profile Picture"
                />
              </div>
              <div>
                <div class="font-medium text-gray-900">{{ user.name }}</div>
                <div class="text-gray-500">{{ user.email }}</div>
              </div>
            </div>
          </TableTd>

          <TableTd>
            {{ user.role }}
          </TableTd>

          <TableTd>
            {{ user.role === 'agent' ? user.department?.name : '--' }}
          </TableTd>

          <TableTd>
            {{ user.created_at }}
          </TableTd>

          <TableTd>
            <TableActions>
              <template v-if="$route.query.trash != 'true'">
                <MenuItem class="cursor-pointer">
                  <div
                    @click="handleEdit(user)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Edit
                  </div>
                </MenuItem>

                <MenuItem class="cursor-pointer">
                  <div
                    @click="handleDelete(user)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Delete
                  </div>
                </MenuItem>
              </template>

              <template v-else>
                <MenuItem class="cursor-pointer">
                  <div
                    @click="handleRestore(user)"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                  >
                    Restore
                  </div>
                </MenuItem>

                <MenuItem class="cursor-pointer">
                  <div
                    @click="handlePermanentDelete(user)"
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
            <p class="p-6 text-center text-xl">No users yet</p>
          </TableTd>
        </tr>
      </template>
    </template>

    <template v-slot:pagination>
      <Pagination
        class="border-t bg-gray-50 py-2 px-3"
        :from="users.from"
        :to="users.to"
        :total="users.total"
        :prev_page_url="users.prev_page_url"
        :next_page_url="users.next_page_url"
        :links="users.links"
        route-name="DashboardUsers"
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
