<script setup lang="ts">
import FilterPanel from '@/components/Common/FilterPanel.vue'
import Autocomplete from '@/components/Forms/Autocomplete.vue'
import Switch from '@/components/Forms/Switch.vue'

import type Option from '@/types/Option'

import type CategoriesFilters from '@/types/CategoriesFilters'

import { onMounted, ref, watch } from 'vue'

const props = defineProps<{
  open: boolean
  filters: CategoriesFilters
  departments: Option[]
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'reset'): void
  (e: 'filter', filters: CategoriesFilters): void
}>()

const role = ref({} as Option)

const department = ref({} as Option)

const trash = ref(false)

onMounted(async () => {
  if (props.filters.trash) {
    trash.value = props.filters.trash.toString() == 'true' ? true : false
  }
})

watch(
  () => props.departments,
  () => {
    if (props.filters.department) {
      department.value = props.departments.find(
        (department: Option) => department.value === props.filters.department?.toString() ?? null
      ) as Option
    }
  }
)

const dates = ref([] as Date[])

const filter = () => {
  const departmentId = department.value?.value
  const filters: CategoriesFilters = {
    trash: trash.value,
    department: departmentId
  }

  emit('filter', filters)
}

const reset = () => {
  trash.value = false
  role.value = {} as Option
  department.value = {} as Option
  dates.value = [] as Date[]

  emit('reset')
}
</script>

<template>
  <FilterPanel :filter="filter" :reset="reset" :open="open" @close="$emit('close')">
    <div class="relative mt-6 flex-1 space-y-6 px-4 sm:px-6">
      <Switch label="Trash" :value="trash" @change="(newValue) => (trash = newValue)" />

      <Autocomplete
        @update="(newDepartment) => (department = newDepartment)"
        label="Department"
        :options="departments"
        :selected="department"
        null-text="All"
      />
    </div>
  </FilterPanel>
</template>
