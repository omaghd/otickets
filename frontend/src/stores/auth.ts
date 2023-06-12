import { defineStore } from 'pinia'

import { computed, ref } from 'vue'

import axios from '@/lib/axios'

import type User from '@/types/User'

type Credentials = {
  name?: string
  email: string
  phone?: string
  password: string
  password_confirmation?: string
}

type ResetPassword = {
  token?: string
  email?: string
  password: string
  password_confirmation: string
}

type Errors = {
  name?: string[]
  email?: string[]
  phone?: string[]
  password?: string[]
  password_confirmation?: string[]
  current_password?: string[]
  picture?: string[]
}

type NewPassword = {
  current_password: string
  password: string
  password_confirmation: string
}

type NewProfile = {
  name: string
  email: string
  phone: string
  picture?: File | null
}

export const useAuthStore = defineStore(
  'auth',
  () => {
    const authUser = ref<User | null>(null)
    const token = ref<string | null>(null)
    const errors = ref<Errors>()
    const isLoading = ref(false)
    const isSuccess = ref(false)
    const message = ref('')

    const user = computed(() => ({ ...authUser.value, token: token.value }))
    const isAdmin = computed(() => authUser.value?.role === 'admin')
    const isAgent = computed(() => authUser.value?.role === 'agent')
    const isManager = computed(() => isAdmin.value || isAgent.value)
    const isClient = computed(() => authUser.value?.role === 'client')

    function $reset() {
      authUser.value = null
      token.value = null
      errors.value = {}
      isLoading.value = false
      isSuccess.value = false
    }

    const login = async (user: Credentials) => {
      $reset()

      isLoading.value = true

      await axios
        .post('/login', user)
        .then((response) => {
          authUser.value = response.data.data.user
          token.value = response.data.data.token

          isSuccess.value = true

          axios.defaults.headers.common['Authorization'] = 'Bearer ' + token.value
        })
        .catch((error) => {
          if (error.response?.status === 401) {
            message.value = error.response.data.message
          } else {
            message.value = 'Please check the errors'
          }

          errors.value = error.response?.data.errors
        })
        .then(() => {
          isLoading.value = false
        })
    }

    const register = async (user: Credentials) => {
      $reset()

      isLoading.value = true

      await axios
        .post('/register', user)
        .then((response) => {
          authUser.value = response.data.data.user
          token.value = response.data.data.token

          isSuccess.value = true

          axios.defaults.headers.common['Authorization'] = 'Bearer ' + token.value
        })
        .catch((error) => {
          if (error.response?.status === 401) {
            message.value = error.response.data.message
          } else {
            message.value = 'Please check the errors'
          }

          errors.value = error.response?.data.errors
        })
        .then(() => {
          isLoading.value = false
        })
    }

    const forgotPassword = async (email: string) => {
      $reset()

      isLoading.value = true

      await axios
        .post('/forgot-password', { email })
        .then((response) => {
          message.value = response.data.message

          isSuccess.value = true
        })
        .catch((error) => {
          errors.value = error.response?.data.errors

          message.value = 'Please check the errors'
        })
        .then(() => {
          isLoading.value = false
        })
    }

    const resetPassword = async (resetPassword: ResetPassword) => {
      $reset()

      isLoading.value = true

      await axios
        .post('/reset-password', resetPassword)
        .then((response) => {
          message.value = response.data.message

          isSuccess.value = true
        })
        .catch((error) => {
          if (error.response?.status === 409) {
            message.value = error.response.data.message
          } else {
            message.value = 'Please check the errors'
          }

          errors.value = error.response?.data.errors
        })
        .then(() => {
          isLoading.value = false
        })
    }

    const logout = async () => {
      await axios
        .post('/logout')
        .then((response) => {
          $reset()

          message.value = response.data.message

          isSuccess.value = true
        })
        .catch((error) => {
          message.value = error.response?.data.message
        })
    }

    const getUserDetails = async () => {
      await axios
        .get('/user')
        .then((response) => {
          authUser.value = response.data

          isSuccess.value = true
        })
        .catch((error) => {
          message.value = error.response?.data.message
        })
    }

    const updateProfile = async (newProfile: NewProfile) => {
      errors.value = {} as Errors
      isLoading.value = true
      isSuccess.value = false

      const headers = { 'Content-Type': 'multipart/form-data' }

      await axios
        .post('/update-profile', { ...newProfile, _method: 'PUT' }, { headers })
        .then(async (response) => {
          await getUserDetails()

          message.value = response.data.message

          isSuccess.value = true
        })
        .catch((error) => {
          errors.value = error.response?.data.errors

          message.value = 'Please check the errors'
        })
        .then(() => {
          isLoading.value = false
        })
    }

    const updatePassword = async (newPassword: NewPassword) => {
      errors.value = {} as Errors
      isLoading.value = true
      isSuccess.value = false

      await axios
        .put('/update-password', newPassword)
        .then((response) => {
          message.value = response.data.message

          isSuccess.value = true
        })
        .catch((error) => {
          if (error.response?.status === 401) {
            message.value = error.response.data.message
          } else {
            message.value = 'Please check the errors'
          }

          errors.value = error.response?.data.errors
        })
        .then(() => {
          isLoading.value = false
        })
    }

    return {
      authUser,
      token,
      user,
      isAdmin,
      isAgent,
      isManager,
      isClient,
      errors,
      isLoading,
      message,
      isSuccess,
      login,
      forgotPassword,
      resetPassword,
      register,
      logout,
      updateProfile,
      updatePassword
    }
  },
  { persist: { paths: ['authUser', 'token'] } }
)
