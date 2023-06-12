import axios from '@/lib/axios'

import type Ticket from '@/types/Ticket'

import { ref } from 'vue'

const getTicketsStats = () => {
  const ticketsStats = ref([] as Ticket[])
  const error = ref(false)
  const isLoading = ref(false)

  const load = async () => {
    isLoading.value = true

    await axios
      .get('/tickets/stats')
      .then((response) => (ticketsStats.value = response.data.data))
      .catch(() => (error.value = true))
      .then(() => (isLoading.value = false))
  }

  return {
    load,
    ticketsStats,
    error,
    isLoading
  }
}

export default getTicketsStats
