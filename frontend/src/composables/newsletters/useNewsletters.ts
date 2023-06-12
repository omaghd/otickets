import { ref } from 'vue'

import axios from '@/lib/axios'

type Errors = {
  email: string[]
}

const errors = ref({} as Errors)
const message = ref('')
const isLoading = ref(false)
const isSuccess = ref(false)

const save = async (email: string, method: string, id: number | null = null) => {
  errors.value = {} as Errors
  isLoading.value = true
  isSuccess.value = false

  const url = id ? `/newsletters/${id}` : '/newsletters'

  await axios
    .post(url, { email, _method: method })
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

const destroy = async (id: number) => {
  isLoading.value = true
  isSuccess.value = false

  await axios
    .delete(`/newsletters/${id}`)
    .then((response) => {
      message.value = response.data.message

      isSuccess.value = true
    })
    .catch((error) => {
      message.value = error.response?.data.message
    })
    .then(() => (isLoading.value = false))
}

const useNewsletters = () => {
  return { errors, isLoading, isSuccess, message, save, destroy }
}

export default useNewsletters
