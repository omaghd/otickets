import axios from '@/lib/axios'

import { ref, type Ref } from 'vue'

import type User from '@/types/User'
import type Paginated from '@/interfaces/Paginated'
import type UsersFilters from '@/types/UsersFilters'

const getUsers = (filters: Ref<UsersFilters>) => {
  const paginatedUsers = ref({} as Paginated<User>)
  const users = ref([] as User[])
  const error = ref(false)
  const isLoading = ref(false)

  const load = async () => {
    isLoading.value = true

    await axios
      .get('/users', { params: filters.value })
      .then((response) => {
        if (filters.value.paginate) paginatedUsers.value = response.data.data
        else users.value = response.data.data
      })
      .catch(() => (error.value = true))
      .then(() => (isLoading.value = false))
  }

  return { load, users, paginatedUsers, error, isLoading }
}

export default getUsers
