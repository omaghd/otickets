<script setup lang="ts">
import {
  Combobox,
  ComboboxLabel,
  ComboboxInput,
  ComboboxButton,
  ComboboxOptions,
  ComboboxOption,
  TransitionRoot
} from '@headlessui/vue'

import CheckIcon from '@heroicons/vue/24/solid/CheckIcon'
import ChevronUpDownIcon from '@heroicons/vue/24/solid/ChevronUpDownIcon'

import { ref, computed, watch } from 'vue'

import type Option from '@/types/Option'

const props = defineProps<{
  label: string
  options: Option[] | any[]
  selected?: Option
  errors?: Array<string>
  nullText?: string
  reset?: boolean
}>()

const selected = ref(props.selected ?? ({} as Option))
let query = ref('')

let filteredItem = computed(() =>
  query.value === ''
    ? props.options
    : props.options.filter(
        (item) =>
          item.name
            .toLowerCase()
            .replace(/\s+/g, '')
            .includes(query.value.toLowerCase().replace(/\s+/g, '')) ||
          (item.email &&
            item.email
              .toLowerCase()
              .replace(/\s+/g, '')
              .includes(query.value.toLowerCase().replace(/\s+/g, '')))
      )
)

const emit = defineEmits<{
  (event: 'update', value: Option): void
  (e: 'reset'): void
}>()

watch(selected, (value) => {
  emit('update', value)
})

watch(
  () => props.selected,
  (value) => {
    selected.value = value ?? ({} as Option)
  }
)

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
  <Combobox as="div" v-model="selected">
    <ComboboxLabel class="block text-sm font-medium text-gray-700">
      {{ label }}
    </ComboboxLabel>

    <div class="relative mt-1">
      <ComboboxInput
        :class="[
          errors?.length
            ? 'border-red-300 pr-10 text-red-900 focus:border-red-300 focus:ring-red-500'
            : 'border-gray-300 text-gray-900 focus:border-teal-300 focus:ring-teal-500'
        ]"
        class="w-full cursor-default rounded-md border bg-white py-2 pl-3 pr-10 text-left shadow-sm focus:outline-none focus:ring-1 sm:text-sm"
        :displayValue="(item) => (item as Option)?.name ?? nullText"
        @change="query = $event.target.value"
      />
      <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
        <ChevronUpDownIcon class="h-5 w-5 text-gray-400" />
      </ComboboxButton>

      <TransitionRoot
        leave="transition ease-in duration-100"
        leaveFrom="opacity-100"
        leaveTo="opacity-0"
        @after-leave="query = ''"
      >
        <ComboboxOptions
          class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
        >
          <div
            v-if="filteredItem.length === 0 && query !== ''"
            class="relative cursor-default select-none py-2 px-4 text-gray-700"
          >
            Nothing found.
          </div>

          <ComboboxOption
            as="template"
            :value="null"
            v-slot="{ active }"
            v-if="filteredItem.length || query === ''"
          >
            <li
              class="relative cursor-default select-none py-2 px-3 text-gray-700"
              :class="{
                'bg-teal-600 text-white': active,
                'text-gray-900': !active
              }"
            >
              <span class="block truncate">{{ nullText }}</span>
            </li>
          </ComboboxOption>

          <ComboboxOption
            v-for="item in filteredItem"
            as="template"
            :key="item.value"
            :value="item"
            v-slot="{ selected, active }"
          >
            <li
              class="relative cursor-default select-none py-2 pl-3 pr-9"
              :class="{
                'bg-teal-600 text-white': active,
                'text-gray-900': !active
              }"
            >
              <div class="flex gap-2">
                <img
                  v-if="item.picture"
                  :src="item.picture"
                  alt="Profile Picture"
                  class="h-8 w-8 flex-shrink-0 rounded-full"
                />

                <div>
                  <p
                    class="block truncate"
                    :class="{ 'font-medium': selected, 'font-normal': !selected }"
                  >
                    {{ item.name }}
                  </p>
                  <div class="text-xs">
                    <p class="block truncate">
                      {{ item.department }}
                    </p>
                    <p class="block truncate">
                      {{ item.email }}
                    </p>
                  </div>
                </div>
              </div>

              <span
                v-if="selected"
                class="absolute inset-y-0 right-0 flex items-center pr-3"
                :class="{ 'text-white': active, 'text-teal-600': !active }"
              >
                <CheckIcon class="h-5 w-5" />
              </span>
            </li>
          </ComboboxOption>
        </ComboboxOptions>
      </TransitionRoot>
    </div>

    <ul v-if="errors?.length">
      <li v-for="error in errors" :key="error" class="mt-1 text-sm text-red-600">
        {{ error }}
      </li>
    </ul>
  </Combobox>
</template>
