import axios from '@/lib/axios'

import { ref, type Ref } from 'vue'

import type Client from '@/types/Client'
import type Paginated from '@/interfaces/Paginated'
import type ClientsFilters from '@/types/ClientsFilters'

const getClients = (filters: Ref<ClientsFilters>) => {
  const clients = ref([] as Client[])
  const paginatedClients = ref({} as Paginated<Client>)
  const error = ref(false)
  const isLoading = ref(false)

  const load = async () => {
    isLoading.value = true

    await axios
      .get('/clients', { params: filters.value })
      .then((response) => {
        if (filters.value.paginate) paginatedClients.value = response.data.data
        else clients.value = response.data.data
      })
      .catch(() => (error.value = true))
      .then(() => (isLoading.value = false))
  }

  return { load, paginatedClients, clients, error, isLoading }
}

export default getClients
