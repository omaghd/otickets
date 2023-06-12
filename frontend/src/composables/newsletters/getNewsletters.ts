import axios from '@/lib/axios'

import { ref } from 'vue'

import type Newsletter from '@/types/Newsletter'
import type Paginated from '@/interfaces/Paginated'
import type NewslettersFilters from '@/types/NewslettersFilters'

const getNewsletters = (filters = ref({} as NewslettersFilters)) => {
  const newsletters = ref({} as Paginated<Newsletter>)
  const error = ref(false)
  const isLoading = ref(false)

  const load = async () => {
    isLoading.value = true

    await axios
      .get('/newsletters', { params: filters.value })
      .then((response) => (newsletters.value = response.data.data))
      .catch(() => (error.value = true))
      .then(() => (isLoading.value = false))
  }

  return { load, newsletters, error, isLoading }
}

export default getNewsletters
