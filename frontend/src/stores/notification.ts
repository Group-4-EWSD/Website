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

    await getNotifications()
      .then((response) => {
        console.log(response.data)
        notifications.value = response.data
        isLoading.value = false
      })
      .catch((error) => {
        isLoading.value = false
        toast.error(error)
        console.error('Error fetching articles:', error)
      })
  }
  const changeNotiSeen = async (notification_id: string) => {
    const noti = notifications.value.find((n) => n.notification_id === notification_id)
    if (noti) {
      noti.seen = 1

      notifications.value = [...notifications.value]

      await changeNotiStatus(noti.notification_id)
    }
  }

  return {
    notifications,
    isLoading,
    fetchNotification,
    changeNotiSeen,
  }
})
