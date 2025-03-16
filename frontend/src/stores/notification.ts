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
        console.log(response.data)
        this.notifications = response.data
      } catch (error) {
        console.error('Error fetching articles:', error)
      }
    },
    changeNotiSeen(action_id: string) {
      const noti = this.notifications.find((n) => n.action_id === action_id)
      if (noti) {
        noti.seen = 1

        this.notifications = [...this.notifications]

        // this.saveNotiSeenToDB(notificationId);
      }
    },
  },
})
