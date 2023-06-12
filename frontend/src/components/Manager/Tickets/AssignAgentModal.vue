<script setup lang="ts">
import Modal from '@/components/Common/Modal.vue'
import TableCard from '@/components/Common/Table/TableCard.vue'
import TableTd from '@/components/Common/Table/TableTd.vue'
import Autocomplete from '@/components/Forms/Autocomplete.vue'
import PrimaryButton from '@/components/Forms/PrimaryButton.vue'

import useTickets from '@/composables/tickets/useTickets'
import getUsers from '@/composables/users/getUsers'

import type Option from '@/types/Option'
import type Ticket from '@/types/Ticket'
import type User from '@/types/User'
import type UsersFilters from '@/types/UsersFilters'

import { ref, watch } from 'vue'

import { useToast } from 'vue-toastification'

const props = defineProps<{
  open: boolean
  ticketToEdit: Ticket
  currentAgents: User[]
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'success'): void
}>()

const agents = ref([] as Option[])

const agent = ref({} as Option)

const userFilters = ref({} as UsersFilters)
const { load, users: tempAgents } = getUsers(userFilters)

const API_URL = import.meta.env.VITE_API_URL

watch(
  () => props.ticketToEdit,
  async (newTicket) => {
    if (newTicket) {
      userFilters.value = {
        role: 'agent',
        department: newTicket.category?.department.id
      }
    }

    await load()

    agents.value = tempAgents.value.map((agent: User) => ({
      name: agent.name,
      value: agent.id.toString(),
      picture: API_URL + agent.picture,
      department: agent.department.name,
      email: agent.email
    }))
  }
)

const resetInput = ref(false)

const toast = useToast()

const { assignAgent, isLoading, isSuccess, errors, message } = useTickets()

const reset = () => {
  resetInput.value = true

  agent.value = {} as Option
}

const onSubmit = async () => {
  await assignAgent(props.ticketToEdit.id, +agent.value?.value, 'agent')

  if (isSuccess.value) {
    toast.success(message.value)

    reset()

    emit('success')

    emit('close')
  } else {
    toast.error(message.value)
  }
}

const headers = ['Agent', 'Assigned By']
</script>

<template>
  <Modal :open="open" title="Assign an agent" width-class="max-w-xl" @close="$emit('close')">
    <div class="p-6" v-if="currentAgents.length">
      <p class="mb-1 font-semibold">History</p>
      <TableCard :headers="headers">
        <tr v-for="agent in currentAgents" :key="agent.id">
          <TableTd v-tooltip.bottom="`Assigned on ${agent.pivot.created_at}`">
            <div class="flex items-start gap-2">
              <div class="h-10 w-10 flex-shrink-0">
                <img
                  class="h-10 w-10 rounded-full"
                  :src="API_URL + agent.picture"
                  alt="Profile Picture"
                />
              </div>
              <div>
                <div class="font-medium text-gray-900">
                  {{ agent.name }}
                </div>
                <div class="text-xs text-gray-500">
                  {{ agent.department.name }}
                </div>
                <div class="text-xs text-gray-500">
                  {{ agent.email }}
                </div>
                <div v-if="agent.pivot.is_current">
                  <span class="rounded-full bg-green-100 px-2 text-xs text-green-800">
                    Current
                  </span>
                </div>
              </div>
            </div>
          </TableTd>

          <TableTd>
            <div class="flex items-start gap-2" v-if="agent.pivot.transferred_by_user?.name">
              <div class="h-10 w-10 flex-shrink-0">
                <img
                  class="h-10 w-10 rounded-full"
                  :src="API_URL + agent.pivot.transferred_by_user?.picture"
                  alt="Profile Picture"
                />
              </div>
              <div>
                <div class="font-medium text-gray-900">
                  {{ agent.pivot.transferred_by_user?.name }}
                </div>
                <template v-if="agent.pivot.transferred_by_user?.role === 'agent'">
                  <div class="text-xs text-gray-500">
                    {{ agent.pivot.transferred_by_user?.department?.name ?? '--' }}
                  </div>
                  <div class="text-xs text-gray-500">
                    {{ agent.pivot.transferred_by_user?.email }}
                  </div>
                </template>

                <div class="text-xs text-gray-500" v-else>Admin</div>
              </div>
            </div>

            <span v-else>--</span>
          </TableTd>
        </tr>
      </TableCard>
    </div>

    <form @submit.prevent="onSubmit">
      <div class="p-6">
        <div class="space-y-6">
          <Autocomplete
            @update="(newAgent) => (agent = newAgent)"
            label="Agent"
            :selected="agent"
            :options="agents"
            :errors="errors?.agent_id"
            null-text="Select an agent"
          />
        </div>
      </div>

      <div class="mt-3 flex justify-end border-t bg-gray-50 px-6 py-3">
        <PrimaryButton type="submit" text="Assign" :loading="isLoading" />
      </div>
    </form>
  </Modal>
</template>
