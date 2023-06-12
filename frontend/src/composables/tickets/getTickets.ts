import axios from '@/lib/axios'

import { ref, type Ref } from 'vue'

import type TicketsFilters from '@/types/TicketsFilters'
import type Paginated from '@/interfaces/Paginated'
import type Ticket from '@/types/Ticket'

const getTickets = (filters: Ref<TicketsFilters>) => {
  const tickets = ref({} as Paginated<Ticket>)
  const error = ref(false)
  const isLoading = ref(true)

  const load = async () => {
    isLoading.value = true
    error.value = false

    await axios
      .get('/tickets', { params: filters.value })
      .then((response) => (tickets.value = response.data.data))
      .catch(() => (error.value = true))
      .finally(() => (isLoading.value = false))
  }

  return { load, tickets, error, isLoading }
}

export default getTickets
