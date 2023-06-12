<script setup lang="ts">
import FilterPanel from '@/components/Common/FilterPanel.vue'
import Autocomplete from '@/components/Forms/Autocomplete.vue'
import Switch from '@/components/Forms/Switch.vue'

import type Option from '@/types/Option'
import type CannedResponsesFilters from '@/types/CannedResponsesFilters'

import { onMounted, ref, watch } from 'vue'

import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

const props = defineProps<{
  open: boolean
  filters: CannedResponsesFilters
  agents: Option[]
  categories: Option[]
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'reset'): void
  (e: 'filter', filters: CannedResponsesFilters): void
}>()

const authStore = useAuthStore()
const { isAdmin } = storeToRefs(authStore)

const agent = ref({} as Option)
const category = ref({} as Option)

const trash = ref(false)

onMounted(async () => {
  if (props.filters.trash) {
    trash.value = props.filters.trash.toString() == 'true' ? true : false
  }
})

watch(
  () => props.agents,
  () => {
    if (props.filters.agent) {
      agent.value = props.agents.find(
        (agent: Option) => agent.value === props.filters.agent?.toString() ?? null
      ) as Option
    }
  }
)

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

const filter = () => {
  const agentId = isNaN(Number(agent.value?.value)) ? null : Number(agent.value?.value)
  const categoryId = isNaN(Number(category.value?.value)) ? null : Number(category.value?.value)
  const filters: CannedResponsesFilters = {
    trash: trash.value,
    agent: agentId,
    category: categoryId
  }

  emit('filter', filters)
}

const reset = () => {
  trash.value = false
  agent.value = {} as Option
  category.value = {} as Option

  emit('reset')
}
</script>

<template>
  <FilterPanel :filter="filter" :reset="reset" :open="open" @close="$emit('close')">
    <div class="relative mt-6 flex-1 space-y-6 px-4 sm:px-6">
      <Switch label="Trash" :value="trash" @change="(newValue) => (trash = newValue)" />

      <Autocomplete
        v-if="isAdmin"
        @update="(newAgent) => (agent = newAgent)"
        label="Agent"
        :options="agents"
        :selected="agent"
        null-text="All"
      />

      <Autocomplete
        @update="(newCategory) => (category = newCategory)"
        label="Category"
        :options="categories"
        :selected="category"
        null-text="All"
      />
    </div>
  </FilterPanel>
</template>
