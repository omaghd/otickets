<script setup lang="ts">
import Pagination from '@/components/Common/Pagination.vue'
import SectionHeader from '@/components/Common/SectionHeader.vue'
import TableCard from '@/components/Common/Table/TableCard.vue'
import TableSkeleton from '@/components/Common/Table/TableSkeleton.vue'
import TableTd from '@/components/Common/Table/TableTd.vue'

import CalendarIcon from '@heroicons/vue/24/outline/CalendarIcon'
import EllipsisHorizontalIcon from '@heroicons/vue/24/outline/EllipsisHorizontalIcon'

import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'

import { useNotificationsStore } from '@/stores/notifications'
import { storeToRefs } from 'pinia'

import { onMounted, ref } from 'vue'

import { useToast } from 'vue-toastification'

import EmptyNotificationsLottie from '@/assets/emptyNotifications.json'
import { onBeforeRouteUpdate } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

withDefaults(
  defineProps<{
    customClass?: string
    showHeader?: boolean
  }>(),
  { showHeader: true }
)

const authStore = useAuthStore()
const { isManager } = storeToRefs(authStore)

const store = useNotificationsStore()
const { isLoading, message, notifications, isSuccess, notificationsCounts } = storeToRefs(store)

const all = ref(true)
const unread = ref(false)

const fetchNotifications = async (withLoading = true, unread = false, page = 1) => {
  await store.getNotifications(withLoading, unread, page)
}

const getAll = async () => {
  if (all.value) return

  await fetchNotifications()

  all.value = true
  unread.value = false
}

const getUnread = async () => {
  if (unread.value) return

  await fetchNotifications(true, true)

  all.value = false
  unread.value = true
}

const toast = useToast()

const headers = ['Message']

const markAsRead = async (id: string) => {
  await store.markAsRead(id)

  if (!isSuccess.value) toast.error(message.value)
  else await fetchNotifications(false, unread.value)
}

const markAllAsRead = async () => {
  if (notificationsCounts.value.total === 0) return

  await store.markAllAsRead()

  if (!isSuccess.value) toast.error(message.value)
  else await fetchNotifications(false, unread.value)
}

const markAsUnread = async (id: string) => {
  await store.markAsUnread(id)

  if (!isSuccess.value) toast.error(message.value)
  else await fetchNotifications(false, unread.value)
}

const deleteNotification = async (id: string) => {
  await store.deleteNotification(id)

  if (!isSuccess.value) toast.error(message.value)
  else await fetchNotifications(false, unread.value)
}

onMounted(async () => {
  await fetchNotifications()

  if (!isSuccess.value) {
    toast.error(message.value)
  }
})

onBeforeRouteUpdate(async (to) => {
  await fetchNotifications(false, unread.value, Number(to.query.page ?? 1))
})
</script>

<template>
  <section :class="customClass">
    <SectionHeader title="Notifications" class="mb-6" v-if="showHeader" />

    <div class="mb-2">
      <div class="h-5 w-60 max-w-full animate-pulse rounded bg-gray-200" v-if="isLoading"></div>

      <div class="flex flex-wrap items-center justify-between gap-3" v-else>
        <div class="flex gap-3 divide-x divide-dashed text-sm text-gray-500">
          <button :class="{ 'font-semibold': all }" @click="getAll">
            All ({{ notificationsCounts.total ?? 0 }})
          </button>

          <div class="pl-3" :class="{ 'font-semibold': unread }">
            <button @click="getUnread">
              Unread Notifications ({{ notificationsCounts.unread ?? 0 }})
            </button>
          </div>
        </div>

        <Menu as="div" class="relative flex">
          <MenuButton>
            <EllipsisHorizontalIcon class="h-5 w-5 text-gray-700 hover:text-gray-800" />
          </MenuButton>

          <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
          >
            <MenuItems
              class="absolute right-0 z-10 mt-5 w-36 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
            >
              <MenuItem class="w-full cursor-pointer">
                <div
                  @click="markAllAsRead"
                  class="block w-fit px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Mark all as read
                </div>
              </MenuItem>
            </MenuItems>
          </transition>
        </Menu>
      </div>
    </div>

    <TableSkeleton v-if="isLoading" />

    <template v-else-if="notificationsCounts.total">
      <TableCard :headers="headers">
        <template v-slot>
          <tr v-if="unread">
            <TableTd colspan="2">
              <p class="p-6 text-center text-xl">No unread notifications</p>
            </TableTd>
          </tr>

          <tr v-for="notification in notifications?.data" :key="notification.id" v-else>
            <TableTd no-padding>
              <component
                :is="notification.data?.reference ? 'router-link' : 'span'"
                :to="{
                  name: isManager ? 'DashboardSingleTicket' : 'ClientSingleTicket',
                  params: { reference: notification.data.reference }
                }"
              >
                <div
                  @click="
                    () =>
                      notification.data.reference &&
                      !notification.read_at &&
                      markAsRead(notification.id)
                  "
                  class="group flex items-center justify-between gap-12 px-3 py-4 hover:bg-gray-50"
                >
                  <div>
                    <div
                      :class="{
                        'text-gray-500': notification.read_at,
                        'font-semibold text-teal-600': !notification.read_at
                      }"
                      class="text-base"
                    >
                      {{ notification.data.message }}
                    </div>
                    <div class="flex items-center gap-1 text-xs">
                      <CalendarIcon class="h-4 w-4 text-gray-400" />
                      <span>{{ notification.created_at }}</span>
                    </div>
                  </div>

                  <div class="flex items-center gap-2">
                    <Menu
                      @click.prevent.stop
                      as="div"
                      class="relative shrink-0 rounded-full border bg-white shadow hover:bg-gray-100 lg:hidden lg:group-hover:flex"
                    >
                      <MenuButton class="p-2">
                        <EllipsisHorizontalIcon class="h-5 w-5" />
                      </MenuButton>

                      <transition
                        enter-active-class="transition ease-out duration-100"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95"
                      >
                        <MenuItems
                          class="absolute right-0 z-10 mt-10 w-fit origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                        >
                          <MenuItem class="cursor-pointer" v-if="!notification.read_at">
                            <div
                              @click="markAsRead(notification.id)"
                              class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                              Mark as read
                            </div>
                          </MenuItem>

                          <MenuItem class="cursor-pointer" v-else>
                            <div
                              @click="markAsUnread(notification.id)"
                              class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                              Mark as unread
                            </div>
                          </MenuItem>

                          <MenuItem class="cursor-pointer">
                            <div
                              @click="deleteNotification(notification.id)"
                              class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                              Delete
                            </div>
                          </MenuItem>
                        </MenuItems>
                      </transition>
                    </Menu>

                    <div
                      class="h-3 w-3 rounded-full bg-teal-500"
                      v-if="!notification.read_at"
                    ></div>
                  </div>
                </div>
              </component>
            </TableTd>
          </tr>
        </template>

        <template v-slot:pagination>
          <Pagination
            class="border-t bg-gray-50 py-2 px-3"
            :from="notifications?.from"
            :to="notifications?.to"
            :total="notifications?.total"
            :prev_page_url="notifications?.prev_page_url"
            :next_page_url="notifications?.next_page_url"
            :links="notifications?.links"
            route-name="ClientNotifications"
          />
        </template>
      </TableCard>
    </template>

    <template v-else>
      <LottieAnimation :animationData="EmptyNotificationsLottie" class="-mt-10 w-60 max-w-full" />
      <p class="mb-6 text-center text-gray-500">You have no notifications yet</p>
    </template>
  </section>
</template>
