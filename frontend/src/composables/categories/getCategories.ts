import axios from '@/lib/axios'

import { ref } from 'vue'

import type Category from '@/types/Category'
import type Paginated from '@/interfaces/Paginated'
import type CategoriesFilters from '@/types/CategoriesFilters'

const getCategories = (filters = ref({} as CategoriesFilters)) => {
  const categories = ref([] as Category[])
  const paginatedCategories = ref({} as Paginated<Category>)
  const error = ref(false)
  const isLoading = ref(true)

  const load = async () => {
    isLoading.value = true

    await axios
      .get('/categories', { params: filters.value })
      .then((response) => {
        if (filters.value.paginate) paginatedCategories.value = response.data.data
        else categories.value = response.data.data
      })
      .catch(() => (error.value = true))
      .then(() => (isLoading.value = false))
  }

  return { load, categories, paginatedCategories, error, isLoading }
}

export default getCategories
