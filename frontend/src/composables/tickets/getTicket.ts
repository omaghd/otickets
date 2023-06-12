import axios from '@/lib/axios'

import type Ticket from '@/types/Ticket'

import { ref } from 'vue'

const getTicket = async (reference: string) => {
  const ticket = ref<Ticket>({} as Ticket)
  const error = ref<boolean>(false)
  const isLoading = ref<boolean>(false)

  await axios
    .get(`/tickets/${reference}`)
    .then((response) => {
      ticket.value = response.data.data

      isLoading.value = false
    })
    .catch(() => (error.value = true))

  return { ticket, error, isLoading }
}

export default getTicket
