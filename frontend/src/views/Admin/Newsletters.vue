<script setup lang="ts">
import TableCard from '@/components/Common/Table/TableCard.vue'
import ActionButton from '@/components/Common/ActionButton.vue'
import TableTd from '@/components/Common/Table/TableTd.vue'
import TableActions from '@/components/Common/Table/TableActions.vue'
import Pagination from '@/components/Common/Pagination.vue'
import TableSkeleton from '@/components/Common/Table/TableSkeleton.vue'
import NotFoundResults from '@/components/Common/NotFoundResults.vue'
import ConfirmationModal from '@/components/Admin/ConfirmationModal.vue'
import NewsletterModal from '@/components/Admin/Newsletters/NewsletterModal.vue'

import { MenuItem } from '@headlessui/vue'

import PlusIcon from '@heroicons/vue/24/outline/PlusIcon'
import MagnifyingGlassIcon from '@heroicons/vue/24/outline/MagnifyingGlassIcon'
import ExclamationTriangleIcon from '@heroicons/vue/24/outline/ExclamationTriangleIcon'

import { useHead } from 'unhead'

import { appTitle } from '@/global'

import { useDashboard } from '@/stores/dashboard'

import getNewsletters from '@/composables/newsletters/getNewsletters'
import useNewsletters from '@/composables/newsletters/useNewsletters'

import { ref, onMounted, watch } from 'vue'

import type Newsletter from '@/types/Newsletter'
import type NewslettersFilters from '@/types/NewslettersFilters'

import { debounce } from 'lodash'

import { onBeforeRouteUpdate, useRoute, useRouter } from 'vue-router'

import { useToast } from 'vue-toastification'

useHead({ title: `Newsletters | ${appTitle}` })

const { setTitle } = useDashboard()

setTitle('Newsletters')

const headers = ['Email', 'IP', 'User Agent', 'Created At', '']

const route = useRoute()

const filters = ref({
  page: isNaN(Number(route.query.page)) ? null : Number(route.query.page),
  query: route.query.query ?? null,
  paginate: true
} as NewslettersFilters)

const { load, isLoading, newsletters } = getNewsletters(filters)

const query = ref((route.query.query as string) ?? '')

const router = useRouter()

watch(
  query,
  debounce(async function (value: string) {
    filters.value.query = value

    const query: any = {}

    if (value) query.query = value

    router.push({ query })
  }, 300)
)

const trash = ref(Boolean(route.query.trash ?? false))

watch(trash, (value) => {
  const query: any = {}

  if (value) query.trash = value

  router.push({ query })
})

onBeforeRouteUpdate(async (to, from, next) => {
  if (to.query.query !== from.query.query || to.query.trash !== from.query.trash) await load()

  next()
})

onMounted(async () => {
  await load()
})

const newsletterModalIsOpen = ref(false)

const handleAfterSave = async (keepQuery: boolean) => {
  if (!Object.keys(route.query).length) await load()
  else if (!keepQuery) router.push({ query: {} })
  else await load()
}

const confirmationModalIsOpen = ref(false)
const confirmationModalTitle = ref('')
const confirmationModalText = ref('')
const confirmationModalButtonText = ref('')
const confirmationModalBackgroundColor = ref('')
const confirmationModalButtonBackgroundColor = ref('')
const confirmationModalColor = ref('')
const confirmationModalIcon = ref({} as any)
const confirmationModalFunction = ref({} as Function)

const { destroy, message, isSuccess, isLoading: operationIsLoading } = useNewsletters()

const handleAdd = () => {
  newsletterToEdit.value = {} as Newsletter

  newsletterModalIsOpen.value = true
}

const newsletterToEdit = ref({} as Newsletter)

const handleEdit = (newsletter: Newsletter) => {
  newsletterToEdit.value = newsletter

  newsletterModalIsOpen.value = true
}

const newsletterToDelete = ref({} as Newsletter)

const handleDelete = (newsletter: Newsletter) => {
  newsletterToDelete.value = newsletter

  confirmationModalIsOpen.value = true
  confirmationModalTitle.value = 'Delete Newsletter'
  confirmationModalText.value = 'Are you sure you want to delete this newsletter?'
  confirmationModalButtonText.value = 'Delete'
  confirmationModalBackgroundColor.value = 'bg-red-100'
  confirmationModalButtonBackgroundColor.value = 'bg-red-600 hover:bg-red-700 focus:ring-red-500'
  confirmationModalColor.value = 'text-red-600'
  confirmationModalIcon.value = ExclamationTriangleIcon
  confirmationModalFunction.value = deleteNewsletter
}

const toast = useToast()

const deleteNewsletter = async () => {
  await destroy(newsletterToDelete.value.id)

  confirmationModalIsOpen.value = false

  if (isSuccess.value) {
    toast.success(message.value)

    await load()
  } else {
    toast.error(message.value)
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
  <NewsletterModal
    :open="newsletterModalIsOpen"
    :newsletterToEdit="newsletterToEdit"
    @close="newsletterModalIsOpen = false"
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

  <header class="mb-3 flex flex-col justify-between gap-3 sm:flex-row-reverse">
    <div class="flex justify-end sm:justify-start">
      <ActionButton :Icon="PlusIcon" text="New Newsletter" :action="handleAdd" />
    </div>

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
  </header>

  <TableSkeleton v-if="isLoading" />

  <NotFoundResults
    v-else-if="!isLoading && !newsletters.data?.length && filters.query"
    text="No newsletters related to your search"
  />

  <TableCard :headers="headers" v-else>
    <template v-slot:default>
      <template v-if="newsletters.data?.length">
        <tr v-for="newsletter in newsletters.data" :key="newsletter.id">
          <TableTd>
            {{ newsletter.email }}
          </TableTd>

          <TableTd>
            {{ newsletter.ip_address ?? '--' }}
          </TableTd>

          <TableTd>
            <p class="w-96 truncate" :title="newsletter.user_agent ?? '--'">
              {{ newsletter.user_agent ?? '--' }}
            </p>
          </TableTd>

          <TableTd>
            {{ newsletter.created_at }}
          </TableTd>

          <TableTd>
            <TableActions>
              <MenuItem class="cursor-pointer">
                <div
                  @click="handleEdit(newsletter)"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Edit
                </div>
              </MenuItem>

              <MenuItem class="cursor-pointer">
                <div
                  @click="handleDelete(newsletter)"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Delete
                </div>
              </MenuItem>
            </TableActions>
          </TableTd>
        </tr>
      </template>

      <template v-else>
        <tr>
          <TableTd colspan="5">
            <p class="p-6 text-center text-xl">No newsletters yet</p>
          </TableTd>
        </tr>
      </template>
    </template>

    <template v-slot:pagination>
      <Pagination
        class="border-t bg-gray-50 py-2 px-3"
        :from="newsletters.from"
        :to="newsletters.to"
        :total="newsletters.total"
        :prev_page_url="newsletters.prev_page_url"
        :next_page_url="newsletters.next_page_url"
        :links="newsletters.links"
        route-name="DashboardNewsletters"
        :query="{
          query: $route.query.query
        }"
      />
    </template>
  </TableCard>
</template>
