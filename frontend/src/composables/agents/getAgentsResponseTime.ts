import axios from '@/lib/axios'

import type MinimalCount from '@/types/MinimalCount'

import { ref } from 'vue'

const getAgentsResponseTime = () => {
  const agentsResponseTime = ref([] as MinimalCount[])
  const error = ref(false)
  const isLoading = ref(false)

  const load = async () => {
    isLoading.value = true

    await axios
      .get('/users/agents-response-time')
      .then((response) => (agentsResponseTime.value = response.data.data))
      .catch(() => (error.value = true))
      .then(() => (isLoading.value = false))
  }

  return { load, agentsResponseTime, error, isLoading }
}

export default getAgentsResponseTime
