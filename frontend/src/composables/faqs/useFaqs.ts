import { ref } from 'vue'

import type Faq from '@/types/Faq'

import axios from '@/lib/axios'

type NewFaq = {
  question: string
  answer: string
  excerpt: string
  slug: string
  is_published: boolean
  category_id: number | null
}

type Errors = {
  question: string[]
  answer: string[]
  excerpt: string[]
  slug: string[]
  is_published: string[]
  category_id: string[]
}

const faqs = ref([] as Faq[])
const errors = ref({} as Errors)
const message = ref('')
const isLoading = ref(false)
const isSuccess = ref(false)

const getSuggestions = async (query: string) => {
  isLoading.value = true
  isSuccess.value = false

  await axios
    .get(`/faqs/suggestions/${query}`)
    .then((response) => {
      faqs.value = response.data.data

      isSuccess.value = true
    })
    .catch((error) => (message.value = error.response?.data.message))
    .then(() => (isLoading.value = false))
}

const save = async (faq: NewFaq, method: string, id: number | null = null) => {
  errors.value = {} as Errors
  isLoading.value = true
  isSuccess.value = false

  const url = id ? `/faqs/${id}` : '/faqs'

  await axios
    .post(url, { ...faq, _method: method })
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
    .put(`/faqs/${id}/restore`)
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

  const url = forceDelete ? `/faqs/${id}/force-delete` : `/faqs/${id}`

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

const useFaqs = () => {
  return { errors, isLoading, isSuccess, message, faqs, getSuggestions, save, restore, destroy }
}

export default useFaqs
