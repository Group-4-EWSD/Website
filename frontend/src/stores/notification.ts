import { defineStore } from 'pinia'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

import { changeNotiStatus, getNotifications } from '@/api/notification'
import type { Notification } from '@/types/notification'

export const useNotificationsStore = defineStore('notifications', () => {
  const notifications = ref<Notification[]>([])
  const isLoading = ref(false)

  const fetchNotification = async () => {
    isLoading.value = true

    try {
      const response = await getNotifications()

      notifications.value = response.data
    } catch (error: any) {
      toast.error(error?.response?.data?.message || 'Failed to fetch notifications.')
      console.error('Error fetching notifications:', error)
    } finally {
      isLoading.value = false
    }
  }

  const changeNotiSeen = async (notification_id: string) => {
    const noti = notifications.value.find((n) => n.notification_id === notification_id)
    if (noti) {
      noti.seen = 1

      notifications.value = [...notifications.value]

      await changeNotiStatus(noti.notification_id)
    }
  }

  function reset() {
    notifications.value = []
    isLoading.value = false
  }

  return {
    notifications,
    isLoading,

    fetchNotification,
    changeNotiSeen,
    reset,
  }
})
