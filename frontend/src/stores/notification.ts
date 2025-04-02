import { defineStore } from 'pinia'
import type { Notification } from '@/types/notification'
import { getNotifications } from '@/api/notification'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

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
  const changeNotiSeen = (notification_id: string) => {
    const noti = notifications.value.find((n) => n.notification_id === notification_id)
    if (noti) {
      noti.seen = 1

      notifications.value = [...notifications.value]

      // this.saveNotiSeenToDB(notificationId);
    }
  }

  return {
    notifications,
    isLoading,
    fetchNotification,
    changeNotiSeen,
  }
})
