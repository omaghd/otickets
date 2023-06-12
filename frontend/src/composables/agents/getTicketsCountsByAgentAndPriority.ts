import axios from '@/lib/axios'

import type MinimalCount from '@/types/MinimalCount'

import { ref } from 'vue'

const getTicketsCountsByAgentAndPriority = () => {
  const ticketsCountsByAgentAndPriority = ref([] as MinimalCount[])
  const error = ref(false)
  const isLoading = ref(false)

  const load = async () => {
    isLoading.value = true

    await axios
      .get('/users/tickets-counts-by-agent-and-priority')
      .then((response) => (ticketsCountsByAgentAndPriority.value = response.data.data))
      .catch(() => (error.value = true))
      .then(() => (isLoading.value = false))
  }

  return {
    load,
    ticketsCountsByAgentAndPriority,
    error,
    isLoading
  }
}

export default getTicketsCountsByAgentAndPriority
