import type Category from '@/types/Category'

import axios from '@/lib/axios'

import { ref, type Ref } from 'vue'

const getCategory = (slug: Ref<string>) => {
  const category = ref({} as Category)
  const error = ref(false)
  const isLoading = ref(false)

  const load = async () => {
    isLoading.value = true

    await axios
      .get(`/categories/${slug.value}`)
      .then((response) => (category.value = response.data.data))
      .catch(() => (error.value = true))
      .then(() => (isLoading.value = false))
  }

  return { load, category, error, isLoading }
}

export default getCategory
