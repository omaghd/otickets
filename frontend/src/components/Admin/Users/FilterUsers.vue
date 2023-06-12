<script setup lang="ts">
import FilterPanel from '@/components/Common/FilterPanel.vue'
import Autocomplete from '@/components/Forms/Autocomplete.vue'
import DatesPicker from '@/components/Forms/DatesPicker.vue'
import ListBox from '@/components/Forms/ListBox.vue'
import Switch from '@/components/Forms/Switch.vue'

import type Option from '@/types/Option'

import type UsersFilters from '@/types/UsersFilters'

import { onMounted, ref, watch, computed } from 'vue'

const props = defineProps<{
  open: boolean
  filters: UsersFilters
  departments: Option[]
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'reset'): void
  (e: 'filter', filters: UsersFilters): void
}>()

const roles = [
  { name: 'Admin', value: 'admin' },
  { name: 'Agent', value: 'agent' }
]

const role = ref({} as Option)

const department = ref({} as Option)

const trash = ref(false)

onMounted(async () => {
  if (props.filters.role) {
    role.value = roles.find(
      (role: Option) => role.value === props.filters.role?.toString() ?? null
    ) as Option
  }

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

const isNotAdmin = computed(() => role.value?.value !== 'admin')

const filter = () => {
  const departmentId = isNaN(Number(department.value?.value))
    ? null
    : Number(department.value?.value)
  const filters: UsersFilters = {
    trash: trash.value,
    role: (role.value?.value as 'admin' | 'agent') ?? null,
    department: isNotAdmin.value ? departmentId : null,
    dates: [
      dates.value?.[0]?.toISOString().slice(0, 10) ?? null,
      dates.value?.[1]?.toISOString().slice(0, 10) ?? null
    ]
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

      <ListBox
        @update="(newRole) => (role = newRole)"
        label="Role"
        :options="roles"
        :selected="role"
        null-text="All"
      />

      <Autocomplete
        v-if="role?.value === 'agent' || !role?.value"
        @update="(newDepartment) => (department = newDepartment)"
        label="Department"
        :options="departments"
        :selected="department"
        null-text="All"
      />

      <DatesPicker @update="(newDates) => (dates = newDates)" :dates="dates" />
    </div>
  </FilterPanel>
</template>
