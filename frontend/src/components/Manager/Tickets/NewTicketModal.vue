<script setup lang="ts">
import Modal from '@/components/Common/Modal.vue'
import Autocomplete from '@/components/Forms/Autocomplete.vue'
import FileUpload from '@/components/Forms/FileUpload.vue'
import FormInput from '@/components/Forms/FormInput.vue'
import ListBox from '@/components/Forms/ListBox.vue'
import PrimaryButton from '@/components/Forms/PrimaryButton.vue'
import RichText from '@/components/Forms/RichText.vue'

import useTickets from '@/composables/tickets/useTickets'

import type Category from '@/types/Category'
import type Option from '@/types/Option'

import { ref, onMounted, watch } from 'vue'

import { useToast } from 'vue-toastification'
import getUsers from '@/composables/users/getUsers'
import type UsersFilters from '@/types/UsersFilters'
import getClients from '@/composables/clients/getClients'
import type User from '@/types/User'
import type Client from '@/types/Client'
import type ClientsFilters from '@/types/ClientsFilters'
import { apiURL } from '@/global'

const props = defineProps<{
  open: boolean
  tempCategories: Category[]
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'success'): void
}>()

const priorities = [
  { name: 'Low', value: 'low' },
  { name: 'Medium', value: 'medium' },
  { name: 'High', value: 'high' }
]

const priority = ref({} as Option)

const categories = ref([] as Option[] | any[])

const category = ref({} as Option | any)

const agents = ref([] as Option[])

const agent = ref({} as Option)

const agentsFilters = ref({} as UsersFilters)
const { load: fetchAgents, users: tempAgents } = getUsers(agentsFilters)

watch(category, async (newCategory) => {
  agent.value = {} as Option

  agentsFilters.value = {
    role: 'agent',
    department: +newCategory.departmentId
  }

  await fetchAgents()

  agents.value = tempAgents.value.map((agent: User) => ({
    name: agent.name,
    value: agent.id.toString(),
    picture: apiURL + agent.picture,
    email: agent.email,
    department: agent.department.name
  }))
})

const clients = ref([] as Option[])

const client = ref({} as Option)

const clientsFilters = ref({ paginate: false } as ClientsFilters)
const { load: fetchClients, clients: tempClients } = getClients(clientsFilters)

onMounted(async () => {
  await fetchClients()

  clients.value = tempClients.value.map((client: Client) => ({
    name: client.name,
    value: client.id.toString(),
    picture: apiURL + client.picture,
    email: client.email
  }))
})

watch(
  () => props.tempCategories,
  (newCategories) => {
    categories.value = newCategories.map((category: Category) => ({
      name: category.name,
      value: category.id.toString(),
      departmentId: category.department.id
    }))
  }
)

const subject = ref('')

const description = ref('')

const files = ref()

const resetInput = ref(false)

const toast = useToast()

const { create, isLoading, isSuccess, errors, message } = useTickets()

const reset = () => {
  resetInput.value = true

  subject.value = ''
  description.value = ''
  files.value = null
  priority.value = {} as Option
  category.value = {} as Option
}

const onSubmit = async () => {
  await create({
    priority: priority.value?.value,
    category_id: category.value?.value,
    subject: subject.value,
    description: description.value,
    attachments: files.value,
    agent_id: isNaN(+agent.value?.value) ? null : +agent.value?.value,
    client_id: isNaN(+client.value?.value) ? null : +client.value?.value
  })

  if (isSuccess.value) {
    toast.success(message.value)

    reset()

    emit('success')

    emit('close')
  } else {
    toast.error(message.value)
  }
}
</script>

<template>
  <Modal :open="open" @close="$emit('close')" title="New Ticket" width-class="max-w-2xl">
    <form @submit.prevent="onSubmit">
      <div class="p-6">
        <div class="space-y-6">
          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <ListBox
              @update="(newPriority) => (priority = newPriority)"
              :selected="priority"
              label="Priority"
              :options="priorities"
              :errors="errors.priority"
              null-text="Select a priority"
            />

            <Autocomplete
              @update="(newCategory) => (category = newCategory)"
              :selected="category"
              label="Category"
              :options="categories"
              :errors="errors.category_id"
              null-text="Select a category"
            />
          </div>

          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <Autocomplete
              @update="(newClient) => (client = newClient)"
              :selected="client"
              label="Client"
              :options="clients"
              :errors="errors.client_id"
              null-text="Select a client"
            />

            <Autocomplete
              @update="(newAgent) => (agent = newAgent)"
              :selected="agent"
              label="Agent"
              :options="agents"
              :errors="errors.agent_id"
              null-text="Random agent"
            />
          </div>

          <FormInput
            @change="(value) => (subject = value)"
            id="subject"
            type="text"
            label="Subject"
            placeholder="Subject"
            :errors="errors.subject"
          />

          <RichText
            @change="(value) => (description = value)"
            label="Description"
            placeholder="Describe your issue..."
            :errors="errors.description"
          />

          <FileUpload
            @change="(value) => (files = value)"
            label="Drop attachments here or click to browse"
            :errors="errors.attachments"
            :ref="files"
            :multiple="true"
          />
        </div>
      </div>

      <div class="mt-3 flex justify-end border-t bg-gray-50 px-6 py-3">
        <PrimaryButton type="submit" text="Create" :loading="isLoading" />
      </div>
    </form>
  </Modal>
</template>
