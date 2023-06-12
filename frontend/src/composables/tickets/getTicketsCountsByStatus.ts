import axios from '@/lib/axios'

import type MinimalCount from '@/types/MinimalCount'

import { ref } from 'vue'

const getTicketsCountsByStatus = () => {
  const ticketsCountsByStatus = ref([] as MinimalCount[])
  const error = ref(false)
  const isLoading = ref(false)

  const load = async () => {
    isLoading.value = true

    await axios
      .get('/tickets/counts-by-status')
      .then((response) => (ticketsCountsByStatus.value = response.data.data))
      .catch(() => (error.value = true))
      .then(() => (isLoading.value = false))
  }

  return { load, ticketsCountsByStatus, error, isLoading }
}

export default getTicketsCountsByStatus
