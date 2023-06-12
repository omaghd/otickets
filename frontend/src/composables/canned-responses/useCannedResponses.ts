import { ref } from 'vue'

import axios from '@/lib/axios'

type NewCannedResponse = {
  title: string
  content: string
  agent_id: number | null
  category_id: number | null
}

type Errors = {
  title: string[]
  content: string[]
  agent_id: string[]
  category_id: string[]
}

const errors = ref({} as Errors)
const message = ref('')
const isLoading = ref(false)
const isSuccess = ref(false)

const save = async (
  cannedResponse: NewCannedResponse,
  method: string,
  id: number | null = null
) => {
  errors.value = {} as Errors
  isLoading.value = true
  isSuccess.value = false

  const url = id ? `/canned-responses/${id}` : '/canned-responses'

  await axios
    .post(url, { ...cannedResponse, _method: method })
    .then((response: any) => {
      message.value = response.data.message

      isSuccess.value = true
    })
    .catch((error: any) => {
      errors.value = error.response?.data.errors

      message.value = 'Please check the errors'
    })
    .then(() => {
      isLoading.value = false
    })
}

const restore = async (id: number) => {
  isLoading.value = true
  isSuccess.value = false

  await axios
    .put(`/canned-responses/${id}/restore`)
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

  const url = forceDelete ? `/canned-responses/${id}/force-delete` : `/canned-responses/${id}`

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

const useCannedResponses = () => {
  return { errors, isLoading, isSuccess, message, save, destroy, restore }
}

export default useCannedResponses
