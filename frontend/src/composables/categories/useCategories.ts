import { ref } from 'vue'

import axios from '@/lib/axios'

type NewCategory = {
  name: string
  slug: string
  department_id: number | null
}

type Errors = {
  name: string[]
  slug: string[]
  department_id: string[]
}

const errors = ref({} as Errors)
const message = ref('')
const isLoading = ref(false)
const isSuccess = ref(false)

const save = async (category: NewCategory, method: string, id: number | null = null) => {
  errors.value = {} as Errors
  isLoading.value = true
  isSuccess.value = false

  const url = id ? `/categories/${id}` : '/categories'

  await axios
    .post(url, { ...category, _method: method })
    .then((response: any) => {
      message.value = response.data.message

      isSuccess.value = true
    })
    .catch((error: any) => {
      errors.value = error.response?.data.errors

      message.value = 'Please check the errors'
    })
    .then(() => (isLoading.value = false))
}

const restore = async (id: number) => {
  isLoading.value = true
  isSuccess.value = false

  await axios
    .put(`/categories/${id}/restore`)
    .then((response) => {
      message.value = response.data.message

      isSuccess.value = true
    })
    .catch((error) => {
      message.value = error.response?.data.message
    })
    .then(() => (isLoading.value = false))
}

const destroy = async (id: number, forceDelete = false) => {
  isLoading.value = true
  isSuccess.value = false

  const url = forceDelete ? `/categories/${id}/force-delete` : `/categories/${id}`

  await axios
    .delete(url)
    .then((response) => {
      message.value = response.data.message

      isSuccess.value = true
    })
    .catch((error) => {
      message.value = error.response?.data.message
    })
    .then(() => (isLoading.value = false))
}

const useCategories = () => {
  return { errors, isLoading, isSuccess, message, save, destroy, restore }
}

export default useCategories
