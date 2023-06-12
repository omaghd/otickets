import axios from '@/lib/axios'

import { ref } from 'vue'

import type Faq from '@/types/Faq'
import type Paginated from '@/interfaces/Paginated'
import type FaqsFilters from '@/types/FaqsFilters'

const getFaqs = (filters = ref({} as FaqsFilters)) => {
  const faqs = ref({} as Paginated<Faq>)
  const error = ref(false)
  const isLoading = ref(false)

  const load = async () => {
    isLoading.value = true

    await axios
      .get('/faqs', { params: filters.value })
      .then((response) => (faqs.value = response.data.data))
      .catch(() => (error.value = true))
      .then(() => (isLoading.value = false))
  }

  return { load, faqs, error, isLoading }
}

export default getFaqs
