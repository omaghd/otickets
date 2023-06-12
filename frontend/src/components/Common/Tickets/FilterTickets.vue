<script setup lang="ts">
import FilterPanel from '@/components/Common/FilterPanel.vue'
import ListBox from '@/components/Forms/ListBox.vue'
import Autocomplete from '@/components/Forms/Autocomplete.vue'
import DatesPicker from '@/components/Forms/DatesPicker.vue'

import { ref, onMounted, watch } from 'vue'

import type Category from '@/types/Category'
import type Option from '@/types/Option'
import type TicketsFilters from '@/types/TicketsFilters'
import type UsersFilters from '@/types/UsersFilters'
import type ClientsFilters from '@/types/ClientsFilters'
import type User from '@/types/User'
import type Client from '@/types/Client'

import getUsers from '@/composables/users/getUsers'
import getClients from '@/composables/clients/getClients'

import { apiURL } from '@/global'

import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'
import Switch from '@/components/Forms/Switch.vue'

const props = defineProps<{
  open: boolean
  filters: TicketsFilters
  tempCategories: Category[]
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'reset'): void
  (e: 'filter', filters: TicketsFilters): void
}>()

const priorities = [
  { name: 'Low', value: 'low' },
  { name: 'Medium', value: 'medium' },
  { name: 'High', value: 'high' }
]

const priority = ref<Option>({} as Option)

const statuses = [
  { name: 'Unassigned', value: 'unassigned' },
  { name: 'Assigned', value: 'assigned' },
  { name: 'Resolved', value: 'resolved' },
  { name: 'Closed', value: 'closed' }
]

const trash = ref(props.filters.trash ?? false)

const status = ref({} as Option)

const categories = ref([] as Option[])

const category = ref({} as Option)

const clients = ref([] as Option[])

const client = ref({} as Option)

const agents = ref([] as Option[])

const agent = ref({} as Option)

const agentFilters = ref({ role: 'agent' } as UsersFilters)
const { load: fetchAgents, users: tempAgents } = getUsers(agentFilters)

const clientFilters = ref({} as ClientsFilters)
const { load: fetchClients, clients: tempClients } = getClients(clientFilters)

const authStore = useAuthStore()
const { isAdmin } = storeToRefs(authStore)

onMounted(async () => {
  if (isAdmin.value) {
    await fetchAgents()

    agents.value = tempAgents.value.map((agent: User) => ({
      name: agent.name,
      value: agent.id.toString(),
      email: agent.email,
      picture: apiURL + agent.picture,
      department: agent.department?.name
    }))

    await fetchClients()

    clients.value = tempClients.value.map((client: Client) => ({
      name: client.name,
      value: client.id.toString(),
      email: client.email,
      picture: apiURL + client.picture
    }))

    if (props.filters.client) {
      client.value = clients.value.find(
        (client: Option) => client.value === props.filters.client?.toString() ?? null
      ) as Option
    }

    if (props.filters.agent) {
      agent.value = agents.value.find(
        (agent: Option) => agent.value === props.filters.agent?.toString() ?? null
      ) as Option
    }
  }

  if (props.filters.priority) {
    priority.value = priorities.find(
      (priority: Option) => priority.value === props.filters.priority?.toString() ?? null
    ) as Option
  }

  if (props.filters.status) {
    status.value = statuses.find(
      (status: Option) => status.value === props.filters.status?.toString() ?? null
    ) as Option
  }

  if (props.filters.category) {
    category.value = categories.value.find(
      (category: Option) => category.value === props.filters.category?.toString() ?? null
    ) as Option
  }

  if (props.filters.dates) {
    dates.value = [
      new Date(props.filters.dates?.[0] as string),
      new Date(props.filters.dates?.[1] as string)
    ]
  }
})

watch(
  () => props.tempCategories,
  (newCategories) => {
    categories.value = newCategories.map((category: Category) => ({
      name: category.name,
      value: category.id.toString()
    }))
  }
)

const dates = ref<Date[]>([] as Date[])

const filter = () => {
  const categoryId = isNaN(Number(category.value?.value)) ? null : Number(category.value?.value)
  const filters: TicketsFilters = {
    trash: trash.value,
    client: client.value?.value ?? null,
    agent: agent.value?.value ?? null,
    status: status.value?.value ?? null,
    priority: priority.value?.value ?? null,
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
  client.value = {} as Option
  agent.value = {} as Option
  priority.value = {} as Option
  status.value = {} as Option
  category.value = {} as Option
  dates.value = [] as Date[]

  emit('reset')
}
</script>

<template>
  <FilterPanel :filter="filter" :reset="reset" :open="open" @close="$emit('close')">
    <div class="relative mt-6 flex-1 space-y-5 px-4 sm:px-6">
      <template v-if="isAdmin">
        <Switch label="Trash" :value="trash" @change="(newValue) => (trash = newValue)" />

        <Autocomplete
          @update="(newClient) => (client = newClient)"
          label="Client"
          :options="clients"
          :selected="client"
          null-text="All"
        />

        <Autocomplete
          @update="(newAgent) => (agent = newAgent)"
          label="Agent"
          :options="agents"
          :selected="agent"
          null-text="All"
        />
      </template>

      <ListBox
        @update="(newPriority) => (priority = newPriority)"
        label="Priority"
        :options="priorities"
        :selected="priority"
        null-text="All"
      />

      <ListBox
        @update="(newStatus) => (status = newStatus)"
        label="Status"
        :options="statuses"
        :selected="status"
        null-text="All"
      />

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
