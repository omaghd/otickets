import { ref } from 'vue'

import axios from '@/lib/axios'

type NewDepartment = {
  name: string
}

type Errors = {
  name: string[]
}

const errors = ref({} as Errors)
const message = ref('')
const isLoading = ref(false)
const isSuccess = ref(false)

const save = async (department: NewDepartment, method: string, id: number | null = null) => {
  errors.value = {} as Errors
  isLoading.value = true
  isSuccess.value = false

  const url = id ? `/departments/${id}` : '/departments'

  await axios
    .post(url, { ...department, _method: method })
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
    .put(`/departments/${id}/restore`)
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

  const url = forceDelete ? `/departments/${id}/force-delete` : `/departments/${id}`

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

const useDepartments = () => {
  return { errors, isLoading, isSuccess, message, save, destroy, restore }
}

export default useDepartments
