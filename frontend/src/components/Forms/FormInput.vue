<script setup lang="ts">
import Errors from './Errors.vue'
import FormLabel from './FormLabel.vue'

import { ExclamationCircleIcon } from '@heroicons/vue/24/solid'

import { watch, ref } from 'vue'

const props = defineProps<{
  value?: string
  label: string
  placeholder?: string
  type: string
  id: string
  disabled?: boolean
  errors?: Array<string>
  Icon?: any
  reset?: boolean
}>()

const emit = defineEmits<{
  (e: 'change', value: string): void
  (e: 'reset'): void
}>()

const value = ref(props.value ?? '')

watch(
  () => props.value,
  (newValue) => {
    emit('change', newValue ?? '')
  }
)

watch(value, (newValue) => {
  emit('change', newValue ?? '')
})

watch(
  () => props.reset,
  () => {
    if (props.reset) {
      value.value = ''

      emit('reset')
    }
  }
)
</script>

<template>
  <div>
    <FormLabel :id="id" :text="label" />

    <div class="relative rounded-md shadow-sm">
      <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
        <component :is="Icon" class="h-5 w-5 text-gray-400" />
      </div>

      <input
        :id="id"
        :type="type"
        v-model="value"
        :placeholder="placeholder"
        :autocomplete="type === 'password' ? 'password' : ''"
        :disabled="disabled"
        :class="[
          'mt-1 block w-full rounded-md focus:outline-none sm:text-sm',
          disabled ? 'bg-gray-100' : '',
          Icon ? 'pl-10' : '',
          errors?.length
            ? 'border-red-300 pr-10 text-red-900 placeholder-red-300 focus:border-red-300 focus:ring-red-500'
            : 'border-gray-300 text-gray-900 placeholder-gray-400 focus:border-teal-300 focus:ring-teal-500'
        ]"
      />

      <div
        v-if="errors?.length"
        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3"
      >
        <ExclamationCircleIcon class="h-5 w-5 text-red-500" />
      </div>
    </div>

    <Errors v-if="errors?.length" :errors="errors" />
  </div>
</template>
