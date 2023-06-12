import axios from '@/lib/axios'

import { ref } from 'vue'

import type CannedResponse from '@/types/CannedResponse'
import type Paginated from '@/interfaces/Paginated'
import type CannedResponsesFilters from '@/types/CannedResponsesFilters'

const getCannedResponses = (filters = ref({} as CannedResponsesFilters)) => {
  const paginatedCannedResponses = ref({} as Paginated<CannedResponse>)
  const cannedResponses = ref([] as CannedResponse[])
  const error = ref(false)
  const isLoading = ref(false)

  const load = async () => {
    isLoading.value = true

    await axios
      .get('/canned-responses', { params: filters.value })
      .then((response) => {
        if (filters.value.paginate) paginatedCannedResponses.value = response.data.data
        else cannedResponses.value = response.data.data
      })
      .catch(() => (error.value = true))
      .then(() => (isLoading.value = false))
  }

  return { load, cannedResponses, paginatedCannedResponses, error, isLoading }
}

export default getCannedResponses
