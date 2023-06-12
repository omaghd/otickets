import { defineStore } from 'pinia'

import axios from '@/lib/axios'

import { ref } from 'vue'

import type PaginatedNotifications from '@/types/PaginatedNotifications'

type NotificationsCounts = {
  total: number
  unread: number
  read: number
}

export const useNotificationsStore = defineStore('notification', () => {
  const notifications = ref({} as PaginatedNotifications)
  const notificationsCounts = ref({} as NotificationsCounts)
  const isLoading = ref(true)
  const isSuccess = ref(false)
  const message = ref('')

  const getCounts = async () => {
    await axios
      .get('/notifications/counts')
      .then((response) => {
        notificationsCounts.value = response.data.data

        isSuccess.value = true
      })
      .catch((error) => {
        message.value = error.response?.data.message
      })
  }

  const getNotifications = async (withLoading = false, unread = false, page = 1) => {
    if (withLoading) isLoading.value = true

    await axios
      .get('/notifications', { params: { unread, page } })
      .then(async (response) => {
        notifications.value = response.data.data

        isSuccess.value = true

        await getCounts()
      })
      .catch((error) => {
        message.value = error.response?.data.message
      })
      .then(() => {
        isLoading.value = false
      })
  }

  const markAsRead = async (notificationId: string) => {
    isSuccess.value = false

    await axios
      .put(`/notifications/${notificationId}/mark-as-read`)
      .then(async (response) => {
        isSuccess.value = true

        message.value = response.data.message
      })
      .catch((error) => {
        message.value = error.response?.data.message
      })
  }

  const markAllAsRead = async () => {
    isSuccess.value = false

    await axios
      .put('/notifications/mark-all-as-read')
      .then(async (response) => {
        message.value = response.data.message

        isSuccess.value = true
      })
      .catch((error) => {
        message.value = error.response?.data.message
      })
  }

  const deleteNotification = async (notificationId: string) => {
    isSuccess.value = false

    await axios
      .delete(`/notifications/${notificationId}`)
      .then(async (response) => {
        message.value = response.data.message

        isSuccess.value = true
      })
      .catch((error) => {
        message.value = error.response?.data.message
      })
  }

  const markAsUnread = async (notificationId: string) => {
    isSuccess.value = false

    await axios
      .put(`/notifications/${notificationId}/mark-as-unread`)
      .then(async (response) => {
        isSuccess.value = true

        message.value = response.data.message
      })
      .catch((error) => {
        message.value = error.response?.data.message
      })
  }

  return {
    notifications,
    isLoading,
    isSuccess,
    message,
    notificationsCounts,
    getNotifications,
    markAsRead,
    markAllAsRead,
    deleteNotification,
    markAsUnread,
    getCounts
  }
})
