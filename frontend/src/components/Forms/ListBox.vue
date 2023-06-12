<script setup lang="ts">
import {
  Listbox,
  ListboxButton,
  ListboxLabel,
  ListboxOption,
  ListboxOptions
} from '@headlessui/vue'

import ChevronUpDownIcon from '@heroicons/vue/24/solid/ChevronUpDownIcon'
import CheckIcon from '@heroicons/vue/24/solid/CheckIcon'

import { ref, watch } from 'vue'

import type Option from '@/types/Option'

const props = defineProps<{
  label: string
  options: Option[]
  selected: Option
  errors?: Array<string>
  nullText?: string
  reset?: boolean
}>()

const selected = ref<Option>(props.selected)

const emit = defineEmits<{
  (event: 'update', value: Option): void
  (e: 'reset'): void
}>()

watch(selected, (value) => {
  emit('update', value)
})

watch(
  () => props.reset,
  () => {
    if (props.reset) {
      selected.value = {} as Option

      emit('reset')
    }
  }
)
</script>

<template>
  <Listbox as="div" v-model="selected">
    <ListboxLabel class="block text-sm font-medium text-gray-700">
      {{ label }}
    </ListboxLabel>
    <div class="relative mt-1">
      <ListboxButton
        :class="[
          errors?.length
            ? 'border-red-300 pr-10 text-red-900 focus:border-red-300 focus:ring-red-500'
            : 'border-gray-300 text-gray-900 focus:border-teal-300 focus:ring-teal-500'
        ]"
        class="relative w-full cursor-default rounded-md border bg-white py-2 pl-3 pr-10 text-left shadow-sm focus:outline-none focus:ring-1 sm:text-sm"
      >
        <span class="block truncate">{{ selected?.name ?? nullText }}</span>
        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
          <ChevronUpDownIcon class="h-5 w-5 text-gray-400" />
        </span>
      </ListboxButton>

      <transition
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <ListboxOptions
          class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
        >
          <ListboxOption as="template" :value="null" v-slot="{ active }">
            <li
              :class="[
                active ? 'bg-teal-600 text-white' : 'text-gray-900',
                'relative cursor-default select-none py-2 pl-3 pr-9'
              ]"
            >
              <span class="block truncate">{{ nullText }}</span>
            </li>
          </ListboxOption>

          <ListboxOption
            as="template"
            v-for="option in options"
            :key="option?.value"
            :value="option"
            v-slot="{ active, selected }"
          >
            <li
              :class="[
                active ? 'bg-teal-600 text-white' : 'text-gray-900',
                'relative cursor-default select-none py-2 pl-3 pr-9'
              ]"
            >
              <span :class="[selected ? 'font-semibold' : 'font-normal', 'block truncate']">
                {{ option?.name }}
              </span>

              <span
                v-if="selected"
                :class="[
                  active ? 'text-white' : 'text-teal-600',
                  'absolute inset-y-0 right-0 flex items-center pr-4'
                ]"
              >
                <CheckIcon class="h-5 w-5" />
              </span>
            </li>
          </ListboxOption>
        </ListboxOptions>
      </transition>
    </div>

    <ul v-if="errors?.length">
      <li v-for="error in errors" :key="error" class="mt-1 text-sm text-red-600">
        {{ error }}
      </li>
    </ul>
  </Listbox>
</template>
