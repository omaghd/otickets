<script setup lang="ts">
import { Switch, SwitchGroup, SwitchLabel } from '@headlessui/vue'

import { ref, watch } from 'vue'

const props = defineProps<{ value?: boolean | null }>()

const emit = defineEmits<{ (e: 'change', toggle: boolean): void }>()

const toggle = ref(props.value ?? false)

watch(toggle, (newToggle) => {
  emit('change', newToggle)
})

watch(
  () => props.value,
  (newValue) => {
    toggle.value = newValue ?? false
  }
)
</script>

<template>
  <SwitchGroup as="div" class="flex items-center gap-2">
    <Switch
      v-model="toggle"
      :class="[
        toggle ? 'bg-teal-600' : 'bg-gray-200',
        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2'
      ]"
    >
      <div
        aria-hidden="true"
        :class="[
          toggle ? 'translate-x-5' : 'translate-x-0',
          'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
        ]"
      ></div>
    </Switch>
    <SwitchLabel as="div">Trash</SwitchLabel>
  </SwitchGroup>
</template>
