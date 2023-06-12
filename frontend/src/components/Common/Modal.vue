<script setup lang="ts">
import SectionHeader from './SectionHeader.vue'

import { Dialog, DialogOverlay, TransitionChild, TransitionRoot } from '@headlessui/vue'

import XMarkIcon from '@heroicons/vue/24/outline/XMarkIcon'

withDefaults(
  defineProps<{
    open: boolean
    title: string
    widthClass?: string
  }>(),
  {
    widthClass: 'max-w-md'
  }
)

defineEmits<{ (e: 'close'): void }>()
</script>

<template>
  <TransitionRoot as="template" :show="open">
    <Dialog as="div" class="fixed inset-0 z-30 overflow-auto" @close="$emit('close')">
      <div class="flex min-h-screen items-center justify-center px-4">
        <TransitionChild
          as="template"
          enter="ease-out duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="ease-in duration-200"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <DialogOverlay class="fixed inset-0 bg-gray-500 bg-opacity-60 transition-opacity" />
        </TransitionChild>

        <span class="hidden sm:inline-block sm:h-screen sm:align-middle">&#8203;</span>
        <TransitionChild
          as="template"
          enter="ease-out duration-300"
          enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          enter-to="opacity-100 translate-y-0 sm:scale-100"
          leave="ease-in duration-200"
          leave-from="opacity-100 translate-y-0 sm:scale-100"
          leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
          <div
            :class="widthClass"
            class="relative inline-block w-full transform rounded-lg bg-white align-middle shadow-xl transition-all sm:my-8"
          >
            <div class="flex items-center justify-between px-6 pt-6">
              <SectionHeader :title="title" class="truncate" />

              <XMarkIcon
                @click="$emit('close')"
                class="h-6 w-6 cursor-pointer text-gray-400 hover:text-gray-500"
              />
            </div>

            <slot></slot>
          </div>
        </TransitionChild>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
