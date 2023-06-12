import axios from '@/lib/axios'

import { ref } from 'vue'

import type Department from '@/types/Department'
import type Paginated from '@/interfaces/Paginated'
import type DepartmentsFilters from '@/types/DepartmentsFilters'

const getDepartments = (filters = ref({} as DepartmentsFilters)) => {
  const departments = ref([] as Department[])
  const paginatedDepartments = ref({} as Paginated<Department>)
  const error = ref(false)
  const isLoading = ref(false)

  const load = async () => {
    isLoading.value = true

    await axios
      .get('/departments', { params: filters.value })
      .then((response) => {
        if (filters.value.paginate) paginatedDepartments.value = response.data.data
        else departments.value = response.data.data
      })
      .catch(() => (error.value = true))
      .then(() => (isLoading.value = false))
  }

  return { load, departments, paginatedDepartments, error, isLoading }
}

export default getDepartments
