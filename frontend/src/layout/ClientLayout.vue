<script setup lang="ts">
import Navbar from '@/components/Layout/Navbar.vue'
import Footer from '@/components/Layout/Footer.vue'

import TicketIcon from '@heroicons/vue/24/outline/TicketIcon'
import UserCircleIcon from '@heroicons/vue/24/outline/UserCircleIcon'
import KeyIcon from '@heroicons/vue/24/outline/KeyIcon'
import BellIcon from '@heroicons/vue/24/outline/BellIcon'
import PlusCircleIcon from '@heroicons/vue/24/outline/PlusCircleIcon'

import { useNotificationsStore } from '@/stores/notifications'
import { storeToRefs } from 'pinia'

const subNavigation = [
  { name: 'Tickets', to: 'ClientTickets', icon: TicketIcon },
  { name: 'New Ticket', to: 'ClientNewTicket', icon: PlusCircleIcon },
  { name: 'Profile', to: 'ClientProfile', icon: UserCircleIcon },
  { name: 'Password', to: 'ClientPassword', icon: KeyIcon },
  { name: 'Notifications', to: 'ClientNotifications', icon: BellIcon }
]

const notificationsStore = useNotificationsStore()
const { notificationsCounts } = storeToRefs(notificationsStore)
</script>

<template>
  <div class="flex min-h-screen flex-col bg-gray-100">
    <Navbar />

    <main class="my-6 flex-1">
      <div class="mx-auto max-w-7xl px-3">
        <div class="rounded-lg border bg-white shadow">
          <div class="divide-y divide-gray-200 lg:grid lg:grid-cols-12 lg:divide-y-0 lg:divide-x">
            <aside class="py-6 lg:col-span-3">
              <nav class="space-y-1">
                <router-link
                  v-for="item in subNavigation"
                  :key="item.name"
                  :to="{ name: item.to }"
                  :class="[
                    $route.name === item.to
                      ? 'border-teal-500 bg-teal-50 text-teal-700 hover:bg-teal-50 hover:text-teal-700'
                      : 'border-transparent text-gray-900 hover:bg-gray-50 hover:text-gray-900',
                    'group flex items-center border-l-4 px-3 py-2 text-sm font-medium'
                  ]"
                >
                  <div class="flex w-full justify-between">
                    <div class="flex items-center gap-3">
                      <component
                        :is="item.icon"
                        :class="[
                          $route.name === item.to
                            ? 'text-teal-500 group-hover:text-teal-500'
                            : 'text-gray-400 group-hover:text-gray-500',
                          '-ml-1 h-6 w-6 flex-shrink-0'
                        ]"
                      />
                      <div class="truncate">
                        {{ item.name }}
                      </div>
                    </div>

                    <div
                      class=""
                      v-if="item.name === 'Notifications' && notificationsCounts.unread"
                    >
                      <span
                        class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800"
                      >
                        {{ notificationsCounts.unread > 99 ? '99+' : notificationsCounts.unread }}
                      </span>
                    </div>
                  </div>
                </router-link>
              </nav>
            </aside>

            <div class="divide-y divide-gray-200 lg:col-span-9">
              <router-view />
            </div>
          </div>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>
