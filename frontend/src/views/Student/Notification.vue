<script setup lang="ts">
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'

import Layout from '@/components/ui/Layout.vue'
import { onMounted, ref } from 'vue'
import type { NotificationList } from '@/types/notification'
import { useNotificationsStore } from '@/stores/notification'

dayjs.extend(relativeTime)

const notificationsStore = useNotificationsStore()

const notifications = ref<NotificationList>([])
// notifications.value = [
//   {
//     notification_id: 1,
//     article_id: 1,
//     name: 'Jean Philippe A.',
//     role: 'Student',
//     faculty: 'Art & Science',
//     message:
//       'Hey, just wanted to remind you about the upcoming project submission deadline. Let me know if you have any questions!',
//     profileImg: '@/assets/profile.png',
//     seen: 1,
//     dateTime: '2025-03-01 14:30',
//   },
//   {
//     notification_id: 2,
//     article_id: 2,
//     name: 'Emily Clark',
//     role: 'Coordinator',
//     faculty: 'Computer Science',
//     message:
//       'Your research paper has been reviewed. Please check the feedback provided in the document.',
//     profileImg: '',
//     seen: 0,
//     dateTime: '2025-03-01 15:45',
//   },
//   {
//     notification_id: 3,
//     article_id: 2,
//     name: 'Emily Clark',
//     role: 'Coordinator',
//     faculty: 'Computer Science',
//     message:
//       'Your research paper has been reviewed. Please check the feedback provided in the document.',
//     seen: 1,
//     profileImg: '',
//     dateTime: '2025-03-01 15:45',
//   },
// ]

onMounted(() => {
  if (!notificationsStore.notifications) {
    notificationsStore.fetchNotification()
  }
})

const handleNotificationClick = (notification: any) => {
  notificationsStore.changeNotiSeen(notification.notification_id)
  notification.seen = 1
}
</script>

<template>
  <Layout>
    <div class="p-6">
      <h1 class="text-2xl font-semibold mb-4">Notifications</h1>

      <!-- Notification Cards -->
      <div class="space-y-4">
        <RouterLink
          v-for="notification in notificationsStore.notifications"
          :key="notification.notification_id"
          :to="`/articles/${notification.article_id}`"
          class="block"
          @click="handleNotificationClick(notification)"
        >
          <div
            class="flex items-start p-4 border rounded-lg shadow-md bg-white hover:bg-gray-50 transition-all cursor-pointer"
          >
            <img
              src="@/assets/profile.png"
              alt="Profile Image"
              class="w-12 h-12 rounded-full border mr-4"
            />

            <div class="flex-1 flex justify-between">
              <!-- Left side -->
              <div>
                <div class="text-lg font-semibold text-gray-800">
                  From {{ notification.name }} ({{ notification.role }} -
                  {{ notification.faculty }})
                </div>
                <p class="text-sm text-gray-600 mt-1">{{ notification.message }}</p>
              </div>

              <!-- Right side -->
              <div class="flex flex-col items-end ml-4 flex-shrink-0 relative">
                <span
                  v-if="!notification.seen"
                  class="absolute top-[-8px] right-0 w-3 h-3 bg-red-500 rounded-full"
                ></span>
                <div class="flex-grow"></div>

                <span class="text-xs text-gray-500">
                  {{ dayjs(notification.dateTime).fromNow() }}
                </span>
              </div>
            </div>
          </div>
        </RouterLink>
      </div>
    </div>
  </Layout>
</template>
