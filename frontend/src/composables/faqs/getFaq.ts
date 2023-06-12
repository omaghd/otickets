import axios from '@/lib/axios'

import type Faq from '@/types/Faq'

import { ref, type Ref } from 'vue'

const getFaq = (slug: Ref<string>, forHome: boolean = false) => {
  const faq = ref({} as Faq)
  const error = ref(false)
  const isLoading = ref(false)

  const load = async () => {
    isLoading.value = true

    await axios
      .get(`/faqs/${slug.value}`, { params: { home: forHome } })
      .then((response) => (faq.value = response.data.data))
      .catch(() => (error.value = true))
      .then(() => (isLoading.value = false))
  }

  return { load, faq, error, isLoading }
}

export default getFaq
