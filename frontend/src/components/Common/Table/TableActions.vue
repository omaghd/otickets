<script setup lang="ts">
import { Menu, MenuButton, MenuItems } from '@headlessui/vue'

import EllipsisHorizontalIcon from '@heroicons/vue/24/outline/EllipsisHorizontalIcon'
</script>

<template>
  <Menu as="div" class="relative">
    <MenuButton class="rounded-full border bg-white p-1 shadow hover:bg-gray-100">
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
        class="absolute right-0 z-10 mt-2 w-fit origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
      >
        <slot></slot>
      </MenuItems>
    </transition>
  </Menu>
</template>
