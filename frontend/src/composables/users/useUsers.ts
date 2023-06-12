import { ref } from 'vue'

import axios from '@/lib/axios'

type NewUser = {
  name: string
  role: string
  email: string
  password: string
  phone: string
  picture: File | null
  department_id: number | null
}

type Errors = {
  name: string[]
  email: string[]
  phone: string[]
  password: string[]
  role: string[]
  picture: string[]
  department_id: string[]
}

const errors = ref({} as Errors)
const message = ref('')
const isLoading = ref(false)
const isSuccess = ref(false)

const save = async (user: NewUser, method: string, id: number | null = null) => {
  errors.value = {} as Errors
  isLoading.value = true
  isSuccess.value = false

  const headers = { 'Content-Type': 'multipart/form-data' }

  const url = id ? `/users/${id}` : '/users'

  await axios
    .post(url, { ...user, _method: method }, { headers })
    .then((response: any) => {
      message.value = response.data.message

      isSuccess.value = true
    })
    .catch((error: any) => {
      errors.value = error.response?.data.errors

      if (error.response?.status === 422) message.value = 'Please check the errors'
      else message.value = error.response?.data.message
    })
    .then(() => {
      isLoading.value = false
    })
}

const restore = async (id: number) => {
  isLoading.value = true
  isSuccess.value = false

  await axios
    .put(`/users/${id}/restore`)
    .then((response) => {
      message.value = response.data.message

      isSuccess.value = true
    })
    .catch((error) => {
      message.value = error.response?.data.message
    })
    .then(() => {
      isLoading.value = false
    })
}

const destroy = async (id: number, forceDelete = false) => {
  isLoading.value = true
  isSuccess.value = false

  const url = forceDelete ? `/users/${id}/force-delete` : `/users/${id}`

  await axios
    .delete(url)
    .then((response) => {
      message.value = response.data.message

      isSuccess.value = true
    })
    .catch((error) => {
      message.value = error.response?.data.message
    })
    .then(() => {
      isLoading.value = false
    })
}

const useUsers = () => {
  return { errors, isLoading, isSuccess, message, save, destroy, restore }
}

export default useUsers
