<script setup lang="ts">
import { Switch, SwitchGroup, SwitchLabel } from '@headlessui/vue'

import { ref, watch } from 'vue'

const props = defineProps<{
  label: string
  value?: boolean
}>()

const emit = defineEmits<{
  (e: 'change', toggle: boolean): void
}>()

const toggle = ref(props.value ?? false)

watch(toggle, (newToggle) => {
  emit('change', newToggle)
})

watch(
  () => props.value,
  (newToggle) => {
    toggle.value = newToggle
  }
)
</script>

<template>
  <SwitchGroup as="div">
    <SwitchLabel>
      <div class="mb-1 block text-sm font-medium text-gray-700">
        {{ label }}
      </div>
    </SwitchLabel>

    <Switch
      v-model="toggle"
      :class="[
        toggle ? 'bg-teal-600' : 'bg-gray-200',
        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2'
      ]"
    >
      <span
        :class="[
          toggle ? 'translate-x-5' : 'translate-x-0',
          'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
        ]"
      />
    </Switch>
  </SwitchGroup>
</template>
