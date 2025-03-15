import { defineStore } from 'pinia'
import type { NotificationList } from '@/types/notification'
import { getNotifications } from '@/api/notification'

export const useNotificationsStore = defineStore('notifications', {
  state: () => ({
    notifications: [] as NotificationList,
  }),
  actions: {
    setNotifications(newNotifications: NotificationList) {
      this.notifications = newNotifications
    },
    async fetchNotification() {
      try {
        const response = await getNotifications()
        this.notifications = response.data
      } catch (error) {
        console.error('Error fetching articles:', error)
      }
    },
    changeNotiSeen(notificationId: number) {
      const noti = this.notifications.find((n) => n.notification_id === notificationId)
      if (noti) {
        noti.seen = 1

        this.notifications = [...this.notifications]

        // this.saveNotiSeenToDB(notificationId);
      }
    },
  },
})
