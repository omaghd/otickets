import axios from '@/lib/axios'

import type MinimalCount from '@/types/MinimalCount'

import { ref } from 'vue'

const getTicketsCountsByCategory = () => {
  const ticketsCountsByCategory = ref([] as MinimalCount[])
  const error = ref(false)
  const isLoading = ref(false)

  const load = async () => {
    isLoading.value = true

    await axios
      .get('/categories/tickets-counts')
      .then((response) => (ticketsCountsByCategory.value = response.data.data))
      .catch(() => (error.value = true))
      .then(() => (isLoading.value = false))
  }

  return {
    load,
    ticketsCountsByCategory,
    error,
    isLoading
  }
}

export default getTicketsCountsByCategory
