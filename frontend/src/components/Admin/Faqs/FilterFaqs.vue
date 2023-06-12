<script setup lang="ts">
import FilterPanel from '@/components/Common/FilterPanel.vue'
import Autocomplete from '@/components/Forms/Autocomplete.vue'
import DatesPicker from '@/components/Forms/DatesPicker.vue'
import Switch from '@/components/Forms/Switch.vue'

import type Option from '@/types/Option'
import type FaqsFilters from '@/types/FaqsFilters'

import { onMounted, ref, watch } from 'vue'

const props = defineProps<{
  open: boolean
  filters: FaqsFilters
  categories: Option[]
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'reset'): void
  (e: 'filter', filters: FaqsFilters): void
}>()

const category = ref({} as Option)

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

watch(
  () => props.categories,
  () => {
    if (props.filters.category) {
      category.value = props.categories.find(
        (category: Option) => category.value === props.filters.category?.toString() ?? null
      ) as Option
    }
  }
)

const dates = ref([] as Date[])

const filter = () => {
  const categoryId = isNaN(Number(category.value?.value)) ? null : Number(category.value?.value)
  const filters: FaqsFilters = {
    trash: trash.value,
    category: categoryId,
    dates: [
      dates.value?.[0]?.toISOString().slice(0, 10) ?? null,
      dates.value?.[1]?.toISOString().slice(0, 10) ?? null
    ]
  }

  emit('filter', filters)
}

const reset = () => {
  trash.value = false
  category.value = {} as Option
  dates.value = [] as Date[]

  emit('reset')
}
</script>

<template>
  <FilterPanel :filter="filter" :reset="reset" :open="open" @close="$emit('close')">
    <div class="relative mt-6 flex-1 space-y-6 px-4 sm:px-6">
      <Switch label="Trash" :value="trash" @change="(newValue) => (trash = newValue)" />

      <Autocomplete
        @update="(newCategory) => (category = newCategory)"
        label="Category"
        :options="categories"
        :selected="category"
        null-text="All"
      />

      <DatesPicker @update="(newDates) => (dates = newDates)" :dates="dates" />
    </div>
  </FilterPanel>
</template>
