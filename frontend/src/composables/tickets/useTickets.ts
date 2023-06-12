import { ref } from 'vue'

import axios from '@/lib/axios'

import type Ticket from '@/types/Ticket'
import type NewTicket from '@/types/NewTicket'
import type NewReply from '@/types/NewReply'

type TicketErrors = {
  subject?: string[]
  description?: string[]
  category_id?: string[]
  priority?: string[]
  attachments?: string[]
}

type ReplyErrors = {
  content?: string[]
  attachments?: string[]
}

type AssignAgentErrors = {
  agent_id: number
  ticket_id: number
}

type UpdateTicket = {
  category_id?: number | null
  priority?: string
}

const ticket = ref({} as Ticket)
const errors = ref([] as TicketErrors & ReplyErrors & AssignAgentErrors & any)
const message = ref('')
const isLoading = ref(false)
const isSuccess = ref(false)

const create = async (newTicket: NewTicket) => {
  errors.value = [] as TicketErrors
  isLoading.value = true

  const headers = { 'Content-Type': 'multipart/form-data' }

  await axios
    .post('/tickets', newTicket, { headers })
    .then((response) => {
      message.value = response.data.message

      ticket.value = response.data.data

      isSuccess.value = true
    })
    .catch((error) => {
      errors.value = error.response?.data.errors

      if (errors.value) {
        const attachmentsErrors = Object.keys(error.response?.data.errors)
          .filter((key) => key.startsWith('attachments.'))
          .flatMap((key) => error.response?.data.errors[key])

        errors.value.attachments = attachmentsErrors
      }

      message.value = 'Please check the errors'
    })
    .then(() => {
      isLoading.value = false
    })
}

const reply = async (newReply: NewReply) => {
  message.value = ''
  errors.value = [] as ReplyErrors
  isLoading.value = true

  const headers = { 'Content-Type': 'multipart/form-data' }

  await axios
    .post('/ticket-replies', newReply, { headers })
    .then((response) => {
      message.value = response.data.message

      isSuccess.value = true
    })
    .catch((error) => {
      errors.value = error.response?.data.errors

      if (errors.value) {
        const attachmentsErrors = Object.keys(error.response?.data.errors)
          .filter((key) => key.startsWith('attachments.'))
          .flatMap((key) => error.response?.data.errors[key])

        errors.value.attachments = attachmentsErrors
      }

      if (error.response.status === 403) message.value = error.response?.data.message
      else message.value = 'Please check the errors'
    })
    .then(() => {
      isLoading.value = false
    })
}

const update = async (ticket: UpdateTicket, id: number) => {
  isLoading.value = true
  isSuccess.value = false

  await axios
    .put(`/tickets/${id}`, ticket)
    .then((response) => {
      message.value = response.data.message

      isSuccess.value = true
    })
    .catch((error) => (message.value = error.response?.data.message))
    .then(() => (isLoading.value = false))
}

const restore = async (id: number) => {
  isLoading.value = true
  isSuccess.value = false

  await axios
    .put(`/tickets/${id}/restore`)
    .then((response) => {
      message.value = response.data.message

      isSuccess.value = true
    })
    .catch((error) => {
      message.value = error.response?.data.message
    })
    .then(() => (isLoading.value = false))
}

const destroy = async (id: number, forceDelete = false) => {
  isLoading.value = true
  isSuccess.value = false

  const url = forceDelete ? `/tickets/${id}/force-delete` : `/tickets/${id}`

  await axios
    .delete(url)
    .then((response) => {
      message.value = response.data.message

      isSuccess.value = true
    })
    .catch((error) => {
      message.value = error.response?.data.message
    })
    .then(() => (isLoading.value = false))
}

const assignAgent = async (ticketId: number, agentId: number, transferTo: 'me' | 'agent') => {
  isLoading.value = true
  isSuccess.value = false

  await axios
    .put(`/assign-agent`, { agent_id: agentId, ticket_id: ticketId, transfer_to: transferTo })
    .then((response) => {
      message.value = response.data.message

      isSuccess.value = true
    })
    .catch((error) => {
      errors.value = error.response?.data.errors

      message.value = error.response?.data.message
    })
    .then(() => (isLoading.value = false))
}

const useTickets = () => {
  return {
    errors,
    isLoading,
    isSuccess,
    message,
    ticket,
    create,
    update,
    reply,
    restore,
    destroy,
    assignAgent
  }
}

export default useTickets
