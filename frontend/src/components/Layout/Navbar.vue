<script setup lang="ts">
import SearchPalette from '@/components/Common/SearchPalette.vue'
import Logo from '@/components/Layout/Logo.vue'

import {
  Disclosure,
  DisclosureButton,
  DisclosurePanel,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems
} from '@headlessui/vue'

import BellIcon from '@heroicons/vue/24/outline/BellIcon'
import Bars3Icon from '@heroicons/vue/24/solid/Bars3Icon'
import XMarkIcon from '@heroicons/vue/24/solid/XMarkIcon'
import CalendarIcon from '@heroicons/vue/24/outline/CalendarIcon'
import EllipsisHorizontalIcon from '@heroicons/vue/24/outline/EllipsisHorizontalIcon'
import MagnifyingGlassIcon from '@heroicons/vue/24/outline/MagnifyingGlassIcon'

import { ref, watch, computed, onMounted } from 'vue'

import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/stores/auth'

import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'

import { useNotificationsStore } from '@/stores/notifications'
import { useSearchPalette } from '@/stores/searchPalette'

import EmptyNotificationsLottie from '@/assets/emptyNotifications.json'

import { useMagicKeys, whenever } from '@vueuse/core'

const store = useAuthStore()
const { isClient, user, message, isSuccess } = storeToRefs(store)

type Navigation = {
  name: string
  to: string
}
const userNavigation = ref<Navigation[]>([])

const navigation = ref<Navigation[]>([])

const updateNavigation = () => {
  if (user.value.id) {
    if (isClient.value) {
      userNavigation.value = [{ name: 'Profile', to: 'ClientProfile' }]

      navigation.value = [{ name: 'Tickets', to: 'ClientTickets' }]
    } else {
      userNavigation.value = [{ name: 'Dashboard', to: 'Dashboard' }]
    }
  } else {
    userNavigation.value = [
      { name: 'Login', to: 'Login' },
      { name: 'Register', to: 'Register' }
    ]

    navigation.value = []
  }
}

const API_URL = import.meta.env.VITE_API_URL

const notificationsStore = useNotificationsStore()
const { notificationsCounts, notifications } = storeToRefs(notificationsStore)

const fetchNotifications = async () => {
  await notificationsStore.getNotifications()
}

onMounted(async () => {
  updateNavigation()

  if (isClient.value) await fetchNotifications()
})

watch(user, () => {
  updateNavigation()
})

const router = useRouter()

const toast = useToast()

const logout = async () => {
  await store.logout()

  if (isSuccess.value) router.push({ name: 'Login' })
  else toast.error(message.value)
}

const picture = computed(() => API_URL + (user.value.picture ?? 'storage/examples/user.jpg'))

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

const searchPaletteStore = useSearchPalette()
const { isOpen } = storeToRefs(searchPaletteStore)

const { current } = useMagicKeys({
  passive: false,
  onEventFired(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k' && e.type === 'keydown') e.preventDefault()
  }
})

whenever(
  () => (current.has('control') || current.has('meta')) && current.has('k'),
  () => (isOpen.value ? searchPaletteStore.close() : searchPaletteStore.open())
)
</script>

<template>
  <SearchPalette />

  <Disclosure
    as="nav"
    class="sticky top-0 z-30 bg-white bg-opacity-50 shadow backdrop-blur-lg backdrop-saturate-150 backdrop-filter firefox:bg-opacity-50"
    v-slot="{ openDisclosure }"
  >
    <div class="mx-auto max-w-7xl px-3">
      <div class="flex h-16 justify-between">
        <div class="flex">
          <div class="flex flex-shrink-0 items-center">
            <Logo class-names="w-10" />
          </div>
          <div class="hidden lg:ml-6 lg:flex lg:space-x-8">
            <router-link
              v-for="item in navigation"
              :key="item.name"
              :to="{ name: item.to }"
              class="inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium"
              :class="[
                $route.name === item.to
                  ? 'border-teal-500 text-gray-900'
                  : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'
              ]"
            >
              {{ item.name }}
            </router-link>
          </div>
        </div>

        <div class="flex items-center lg:hidden">
          <button
            @click="searchPaletteStore.open"
            type="button"
            class="mr-4 block rounded-full p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
          >
            <span class="sr-only">Search</span>
            <MagnifyingGlassIcon class="h-6 w-6" />
          </button>

          <DisclosureButton
            class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal-500"
          >
            <span class="sr-only">Open main menu</span>
            <Bars3Icon v-if="!openDisclosure" class="block h-6 w-6" />
            <XMarkIcon v-else class="block h-6 w-6" />
          </DisclosureButton>
        </div>

        <div class="hidden lg:ml-4 lg:flex lg:items-center">
          <button
            @click="searchPaletteStore.open"
            type="button"
            class="mr-4 block rounded-full p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
          >
            <span class="sr-only">Search</span>
            <MagnifyingGlassIcon class="h-6 w-6" />
          </button>

          <Menu as="div" class="relative flex-shrink-0">
            <MenuButton
              v-if="isClient"
              type="button"
              class="mr-4 flex rounded-full p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
            >
              <span class="sr-only">View notifications</span>
              <div class="relative">
                <BellIcon class="h-6 w-6" />
                <span class="absolute top-0 right-0 flex h-3 w-3" v-if="notificationsCounts.unread">
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
                class="absolute right-0 z-10 mt-2 w-96 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
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
                      name: 'ClientSingleTicket',
                      params: { reference: notification.data.reference }
                    }"
                  >
                    <MenuItem class="cursor-pointer">
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

                  <router-link :to="{ name: 'ClientNotifications' }">
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

          <Menu as="div" class="relative flex-shrink-0">
            <div>
              <MenuButton
                class="flex rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
              >
                <span class="sr-only">Open user menu</span>
                <img
                  class="h-8 w-8 rounded-full border object-cover"
                  :src="picture"
                  alt="Profile Picture"
                />
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
                class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
              >
                <router-link
                  :to="{ name: item.to }"
                  v-for="item in userNavigation"
                  :key="item.name"
                >
                  <MenuItem>
                    <div class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                      {{ item.name }}
                    </div>
                  </MenuItem>
                </router-link>

                <MenuItem v-if="user?.id" class="cursor-pointer">
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

    <DisclosurePanel class="lg:hidden" v-slot="{ close }">
      <div class="space-y-1 pt-2 pb-3" v-if="navigation.length">
        <router-link v-for="item in navigation" :key="item.name" :to="{ name: item.to }">
          <DisclosureButton
            :class="[
              {
                'border-l-4 border-l-teal-500 bg-teal-50 text-teal-700': $route.name === item.to
              },
              'text-gray-600 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-800'
            ]"
            class="w-full py-2 pl-3 pr-4 text-left text-base font-medium"
          >
            {{ item.name }}
          </DisclosureButton>
        </router-link>
      </div>
      <div class="border-t border-gray-200 pt-4 pb-3">
        <div class="mb-3 flex items-center px-4" v-if="isClient">
          <div class="flex-shrink-0">
            <img
              class="h-10 w-10 rounded-full border object-cover"
              :src="picture"
              alt="Profile Picture"
            />
          </div>
          <div class="ml-3">
            <div class="text-base font-medium text-gray-800">{{ user.name }}</div>
            <div class="text-sm font-medium text-gray-500">{{ user.email }}</div>
          </div>
          <router-link
            @click="close"
            :to="{ name: 'ClientNotifications' }"
            class="relative ml-auto flex-shrink-0 rounded-full bg-white p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
          >
            <span class="sr-only">View notifications</span>
            <BellIcon class="h-6 w-6" />

            <span class="absolute top-1 right-1 flex h-3 w-3" v-if="notificationsCounts.unread">
              <span
                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-teal-400 opacity-75"
              ></span>
              <span class="relative inline-flex h-3 w-3 rounded-full bg-teal-500"></span>
            </span>
          </router-link>
        </div>

        <div class="space-y-1">
          <router-link v-for="item in userNavigation" :key="item.name" :to="{ name: item.to }">
            <DisclosureButton
              class="w-full px-4 py-2 text-left text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800"
            >
              {{ item.name }}
            </DisclosureButton>
          </router-link>

          <DisclosureButton
            v-if="isClient"
            class="w-full px-4 py-2 text-left text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800"
            @click="logout"
          >
            Logout
          </DisclosureButton>
        </div>
      </div>
    </DisclosurePanel>
  </Disclosure>
</template>
