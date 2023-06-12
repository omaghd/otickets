<script setup lang="ts">
import FilterPanel from '@/components/Common/FilterPanel.vue'
import DatesPicker from '@/components/Forms/DatesPicker.vue'
import Switch from '@/components/Forms/Switch.vue'

import type ClientsFilters from '@/types/ClientsFilters'

import { onMounted, ref } from 'vue'

const props = defineProps<{
  open: boolean
  filters: ClientsFilters
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'reset'): void
  (e: 'filter', filters: ClientsFilters): void
}>()

const trash = ref(false)

onMounted(async () => {
  if (props.filters.dates) {
    dates.value = [
      new Date(props.filters.dates?.[0] as string),
      new Date(props.filters.dates?.[1] as string)
    ]
  }

  if (props.filters.trash) {
    trash.value = props.filters.trash.toString() == 'true' ? true : false
  }
})

const dates = ref([] as Date[])

const filter = () => {
  const filters: ClientsFilters = {
    trash: trash.value,
    dates: [
      dates.value?.[0]?.toISOString().slice(0, 10) ?? null,
      dates.value?.[1]?.toISOString().slice(0, 10) ?? null
    ]
  }

  emit('filter', filters)
}

const reset = () => {
  trash.value = false
  dates.value = [] as Date[]

  emit('reset')
}
</script>

<template>
  <FilterPanel :filter="filter" :reset="reset" :open="open" @close="$emit('close')">
    <div class="relative mt-6 flex-1 space-y-6 px-4 sm:px-6">
      <Switch label="Trash" :value="trash" @change="(newValue) => (trash = newValue)" />

      <DatesPicker @update="(newDates) => (dates = newDates)" :dates="dates" />
    </div>
  </FilterPanel>
</template>
