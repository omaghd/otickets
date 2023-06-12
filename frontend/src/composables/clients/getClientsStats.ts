import axios from '@/lib/axios'

import type Client from '@/types/Client'

import { ref } from 'vue'

const getClientsStats = () => {
  const clientsStats = ref([] as Client[])
  const error = ref(false)
  const isLoading = ref(false)

  const load = async () => {
    isLoading.value = true

    await axios
      .get('/users/clients-stats')
      .then((response) => (clientsStats.value = response.data.data))
      .catch(() => (error.value = true))
      .then(() => (isLoading.value = false))
  }

  return { load, clientsStats, error, isLoading }
}

export default getClientsStats
