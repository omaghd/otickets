<script setup lang="ts">
import Logo from '@/components/Layout/Logo.vue'

import { ref, computed, onMounted } from 'vue'

import {
  Dialog,
  DialogOverlay,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
  TransitionChild,
  TransitionRoot
} from '@headlessui/vue'

import EllipsisHorizontalIcon from '@heroicons/vue/24/outline/EllipsisHorizontalIcon'
import CalendarIcon from '@heroicons/vue/24/outline/CalendarIcon'
import XMarkIcon from '@heroicons/vue/24/outline/XMarkIcon'
import Bars2Icon from '@heroicons/vue/24/outline/Bars2Icon'
import HomeIcon from '@heroicons/vue/24/outline/HomeIcon'
import UsersIcon from '@heroicons/vue/24/outline/UsersIcon'
import BriefcaseIcon from '@heroicons/vue/24/outline/BriefcaseIcon'
import UserGroupIcon from '@heroicons/vue/24/outline/UserGroupIcon'
import Squares2X2Icon from '@heroicons/vue/24/outline/Squares2X2Icon'
import QuestionMarkCircleIcon from '@heroicons/vue/24/outline/QuestionMarkCircleIcon'
import BellIcon from '@heroicons/vue/24/outline/BellIcon'
import ChatBubbleLeftRightIcon from '@heroicons/vue/24/outline/ChatBubbleLeftRightIcon'
import TicketIcon from '@heroicons/vue/24/outline/TicketIcon'
import EnvelopeIcon from '@heroicons/vue/24/outline/EnvelopeIcon'
import UserCircleIcon from '@heroicons/vue/24/outline/UserCircleIcon'
import KeyIcon from '@heroicons/vue/24/outline/KeyIcon'

import { useRoute, useRouter } from 'vue-router'

import { useToast } from 'vue-toastification'

import { useAuthStore } from '@/stores/auth'
import { useDashboard } from '@/stores/dashboard'
import { useNotificationsStore } from '@/stores/notifications'
import { storeToRefs } from 'pinia'

import EmptyNotificationsLottie from '@/assets/emptyNotifications.json'

const userNavigation = [{ name: 'Profile', to: 'DashboardProfile' }]

const sidebarOpen = ref(false)

const userStore = useAuthStore()
const { user, isAdmin, message, isSuccess } = storeToRefs(userStore)

const adminNavigation = [
  { name: 'Dashboard', to: 'Dashboard', icon: HomeIcon },
  { name: 'Users', to: 'DashboardUsers', icon: UsersIcon },
  { name: 'Departments', to: 'DashboardDepartments', icon: BriefcaseIcon },
  { name: 'Clients', to: 'DashboardClients', icon: UserGroupIcon },
  { name: 'Categories', to: 'DashboardCategories', icon: Squares2X2Icon },
  { name: 'FAQs', to: 'DashboardFaqs', icon: QuestionMarkCircleIcon },
  { name: 'Canned Responses', to: 'DashboardCannedResponses', icon: ChatBubbleLeftRightIcon },
  { name: 'Newsletters', to: 'DashboardNewsletters', icon: EnvelopeIcon },
  { name: 'Tickets', to: 'DashboardTickets', icon: TicketIcon },
  { name: 'Profile', to: 'DashboardProfile', icon: UserCircleIcon },
  { name: 'Password', to: 'DashboardPassword', icon: KeyIcon },
  { name: 'Notifications', to: 'DashboardNotifications', icon: BellIcon }
]

const agentNavigation = [
  { name: 'Dashboard', to: 'Dashboard', icon: HomeIcon },
  { name: 'Tickets', to: 'DashboardTickets', icon: TicketIcon },
  { name: 'Canned Responses', to: 'DashboardCannedResponses', icon: ChatBubbleLeftRightIcon },
  { name: 'Profile', to: 'DashboardProfile', icon: UserCircleIcon },
  { name: 'Password', to: 'DashboardPassword', icon: KeyIcon },
  { name: 'Notifications', to: 'DashboardNotifications', icon: BellIcon }
]

const navigation = computed(() => {
  if (isAdmin.value) return adminNavigation
  else return agentNavigation
})

const API_URL = import.meta.env.VITE_API_URL

const picture = computed(() => API_URL + user.value.picture)

const router = useRouter()

const toast = useToast()

const logout = async () => {
  await userStore.logout()

  if (isSuccess.value) router.push({ name: 'Login' })
  else toast.error(message.value)
}

const dashboardStore = useDashboard()
const { title } = storeToRefs(dashboardStore)

const notificationsStore = useNotificationsStore()
const { notificationsCounts, notifications } = storeToRefs(notificationsStore)

const fetchNotifications = async () => {
  await notificationsStore.getNotifications()
}

const markAsRead = async (id: string) => {
  await notificationsStore.markAsRead(id)

  await fetchNotifications()
}

const markAllAsRead = async () => {
  if (notificationsCounts.value.total === 0) return

  await notificationsStore.markAllAsRead()

  await fetchNotifications()
}

const markAsUnread = async (id: string) => {
  await notificationsStore.markAsUnread(id)

  await fetchNotifications()
}

const deleteNotification = async (id: string) => {
  await notificationsStore.deleteNotification(id)

  await fetchNotifications()
}

const route = useRoute()

onMounted(async () => {
  if (route.name !== 'DashboardNotifications') await fetchNotifications()
})
</script>

<template>
  <div>
    <TransitionRoot as="template" :show="sidebarOpen">
      <Dialog as="div" class="fixed inset-0 z-40 flex md:hidden" @close="sidebarOpen = false">
        <TransitionChild
          as="template"
          enter="transition-opacity ease-linear duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="transition-opacity ease-linear duration-300"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <DialogOverlay class="fixed inset-0 bg-gray-600 bg-opacity-75" />
        </TransitionChild>
        <TransitionChild
          as="template"
          enter="transition ease-in-out duration-300 transform"
          enter-from="-translate-x-full"
          enter-to="translate-x-0"
          leave="transition ease-in-out duration-300 transform"
          leave-from="translate-x-0"
          leave-to="-translate-x-full"
        >
          <div class="relative flex w-full max-w-xs flex-1 flex-col bg-white pt-5 pb-4">
            <TransitionChild
              as="template"
              enter="ease-in-out duration-300"
              enter-from="opacity-0"
              enter-to="opacity-100"
              leave="ease-in-out duration-300"
              leave-from="opacity-100"
              leave-to="opacity-0"
            >
              <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button
                  type="button"
                  class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                  @click="sidebarOpen = false"
                >
                  <span class="sr-only">Close sidebar</span>
                  <XMarkIcon class="h-6 w-6 text-white" />
                </button>
              </div>
            </TransitionChild>
            <div class="flex flex-shrink-0 items-center px-4">
              <Logo class-names="w-12" with-text />
            </div>
            <div class="mt-5 h-0 flex-1 overflow-y-auto">
              <nav class="space-y-1 px-2">
                <router-link
                  @click="sidebarOpen = false"
                  v-for="item in navigation"
                  :key="item.name"
                  :to="{ name: item.to }"
                  :class="[
                    $route.name === item.to
                      ? 'bg-gray-100 text-gray-900'
                      : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                    'group flex items-center justify-between rounded-md px-2 py-2 text-base font-medium'
                  ]"
                >
                  <div class="flex items-center gap-3">
                    <component
                      :is="item.icon"
                      :class="[
                        $route.name === item.to
                          ? 'text-gray-500'
                          : 'text-gray-400 group-hover:text-gray-500',
                        ' h-6 w-6 flex-shrink-0'
                      ]"
                    />
                    {{ item.name }}
                  </div>

                  <div v-if="item.name === 'Notifications' && notificationsCounts.unread">
                    <span
                      class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800"
                    >
                      {{ notificationsCounts.unread > 99 ? '99+' : notificationsCounts.unread }}
                    </span>
                  </div>
                </router-link>
              </nav>
            </div>
          </div>
        </TransitionChild>
        <div class="w-14 flex-shrink-0"></div>
      </Dialog>
    </TransitionRoot>

    <div class="hidden md:fixed md:inset-y-0 md:flex md:w-64 md:flex-col">
      <div class="flex flex-grow flex-col overflow-y-auto border-r border-gray-200 bg-white pt-5">
        <div class="flex flex-shrink-0 items-center gap-1 px-4">
          <Logo class-names="w-12" with-text />
        </div>
        <div class="mt-5 flex flex-grow flex-col">
          <nav class="flex-1 space-y-1 px-2 pb-4">
            <router-link
              v-for="item in navigation"
              :key="item.name"
              :to="{ name: item.to }"
              :class="[
                $route.name === item.to
                  ? 'bg-gray-100 text-gray-900'
                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                'group flex items-center justify-between rounded-md px-2 py-2 text-sm font-medium'
              ]"
            >
              <div class="flex items-center gap-3">
                <component
                  :is="item.icon"
                  :class="[
                    $route.name === item.to
                      ? 'text-gray-500'
                      : 'text-gray-400 group-hover:text-gray-500',
                    'h-6 w-6 flex-shrink-0'
                  ]"
                />
                {{ item.name }}
              </div>

              <div v-if="item.name === 'Notifications' && notificationsCounts.unread">
                <span
                  class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800"
                >
                  {{ notificationsCounts.unread > 99 ? '99+' : notificationsCounts.unread }}
                </span>
              </div>
            </router-link>
          </nav>
        </div>
      </div>
    </div>
    <div class="flex flex-1 flex-col md:pl-64">
      <div class="sticky top-0 z-20 flex h-16 flex-shrink-0 bg-white shadow">
        <button
          type="button"
          class="border-r border-gray-200 px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500 md:hidden"
          @click="sidebarOpen = true"
        >
          <span class="sr-only">Open sidebar</span>
          <Bars2Icon class="h-6 w-6" />
        </button>
        <div class="flex flex-1 justify-end px-4 sm:px-6 md:px-8">
          <div class="ml-4 flex items-center gap-3 md:ml-6">
            <Menu as="div">
              <MenuButton
                type="button"
                class="flex rounded-full p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
              >
                <span class="sr-only">View notifications</span>
                <div class="relative">
                  <BellIcon class="h-6 w-6" />
                  <span
                    class="absolute top-0 right-0 flex h-3 w-3"
                    v-if="notificationsCounts.unread"
                  >
                    <span
                      class="absolute inline-flex h-full w-full animate-ping rounded-full bg-teal-400 opacity-75"
                    ></span>
                    <span class="relative inline-flex h-3 w-3 rounded-full bg-teal-500"></span>
                  </span>
                </div>
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
                  class="absolute right-0 z-10 mt-2 w-96 max-w-full origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                >
                  <div class="flex items-center justify-between border-b p-3">
                    <div class="flex items-center gap-1">
                      <div class="font-semibold">Notifications</div>
                      <div class="text-sm" v-if="notificationsCounts.unread">
                        ({{ notificationsCounts.unread }} unread)
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
                          <MenuItem class="cursor-pointer">
                            <div
                              @click="markAllAsRead"
                              class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                              Mark all as read
                            </div>
                          </MenuItem>
                        </MenuItems>
                      </transition>
                    </Menu>
                  </div>

                  <template v-if="notifications.total">
                    <component
                      v-for="notification in notifications.data"
                      :key="notification.id"
                      :is="notification.data?.reference ? 'router-link' : 'span'"
                      :to="{
                        name: 'DashboardSingleTicket',
                        params: { reference: notification.data.reference }
                      }"
                    >
                      <MenuItem
                        :class="{
                          'cursor-pointer': notification.data.reference && !notification.read_at
                        }"
                      >
                        <div
                          @click="
                            () =>
                              notification.data.reference &&
                              !notification.read_at &&
                              markAsRead(notification.id)
                          "
                          class="group flex items-center justify-between gap-3 px-3 py-4 hover:bg-gray-50"
                        >
                          <div class="flex flex-col overflow-hidden">
                            <div
                              :class="{
                                'text-gray-600': notification.read_at,
                                'font-semibold text-teal-600': !notification.read_at
                              }"
                              class="truncate text-base"
                              :title="notification.data.message"
                            >
                              {{ notification.data.message }}
                            </div>
                            <div class="flex items-center gap-1 text-xs">
                              <CalendarIcon class="h-4 w-4 text-gray-400" />
                              <span :class="{ 'text-gray-600': notification.read_at }">
                                {{ notification.created_at }}
                              </span>
                            </div>
                          </div>

                          <div class="flex items-center gap-2">
                            <Menu
                              @click.prevent.stop
                              as="div"
                              class="relative hidden shrink-0 rounded-full border bg-white shadow hover:bg-gray-100 group-hover:flex"
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
                                  class="absolute right-0 z-10 mt-10 w-32 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
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
                      </MenuItem>
                    </component>

                    <router-link :to="{ name: 'DashboardNotifications' }">
                      <MenuItem>
                        <div
                          class="border-t bg-white p-3 text-center text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                          View all
                        </div>
                      </MenuItem>
                    </router-link>
                  </template>

                  <template v-else>
                    <LottieAnimation
                      :animationData="EmptyNotificationsLottie"
                      class="w-40 max-w-full"
                    />
                    <p class="mb-12 text-center text-gray-500">You have no notifications yet</p>
                  </template>
                </MenuItems>
              </transition>
            </Menu>

            <Menu as="div" class="relative">
              <div>
                <MenuButton
                  class="flex max-w-xs items-center rounded-full bg-white text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
                >
                  <span class="sr-only">Open user menu</span>
                  <img class="h-8 w-8 rounded-full" :src="picture" alt="Profile Picture" />
                </MenuButton>
              </div>
              <transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
              >
                <MenuItems
                  class="absolute right-0 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                >
                  <router-link
                    v-for="item in userNavigation"
                    :key="item.name"
                    :to="{ name: item.to }"
                  >
                    <MenuItem>
                      <div class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        {{ item.name }}
                      </div>
                    </MenuItem>
                  </router-link>

                  <MenuItem class="cursor-pointer">
                    <div
                      @click="logout"
                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                      Logout
                    </div>
                  </MenuItem>
                </MenuItems>
              </transition>
            </Menu>
          </div>
        </div>
      </div>

      <main class="flex-1">
        <div class="py-6">
          <div class="mx-auto max-w-7xl px-4 sm:px-6 md:px-8">
            <h1 class="text-2xl font-semibold text-gray-900">
              {{ title }}
            </h1>
          </div>
          <div class="mx-auto max-w-7xl px-4 sm:px-6 md:px-8">
            <div class="py-4">
              <router-view />
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>
