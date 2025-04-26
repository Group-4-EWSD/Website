<script setup lang="ts">
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import { onMounted } from 'vue'

import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import Button from '@/components/ui/button/Button.vue'
import Layout from '@/components/ui/Layout.vue'
import Skeleton from '@/components/ui/skeleton/Skeleton.vue'
import { getInitials } from '@/lib/utils'
import { useNotificationsStore } from '@/stores/notification'

dayjs.extend(relativeTime)

const notificationsStore = useNotificationsStore()

onMounted(() => {
  if (!notificationsStore.notifications.length) notificationsStore.fetchNotification()
})

const handleNotificationClick = (notification: any) => {
  notificationsStore.changeNotiSeen(notification.notification_id)
  notification.seen = 1
}

const notificationMessage = (type: number) => {
  switch (type) {
    case 1:
      return 'Liked your article'
    case 2:
      return 'Commented on your article'
    case 3:
      return 'Wrote a feedback on your article'
    case 4:
      return 'Uploaded an article'
    case 5:
      return 'Updated the article'
    case 6:
      return 'Published the article'
    default:
      return 'You have a notification'
  }
}
</script>

<template>
  <Layout>
    <h1 class="text-2xl font-semibold mb-4">Notifications</h1>

    <div class="space-y-4">
      <template v-if="notificationsStore.isLoading">
        <div
          v-for="n in 5"
          :key="n"
          class="flex items-start p-4 border rounded-lg bg-white flex-col sm:flex-row sm:items-center"
        >
          <Skeleton class="w-12 h-12 rounded-full mb-2 sm:mb-0" />
          <div
            class="flex-1 flex flex-col sm:flex-row sm:justify-between sm:items-start sm:ml-4 w-full"
          >
            <div class="space-y-2 w-full sm:w-auto">
              <Skeleton class="h-5 w-full sm:w-48 rounded" />
              <Skeleton class="h-4 w-full sm:w-64 rounded" />
            </div>
            <div class="flex justify-end sm:ml-4 mt-2 sm:mt-0">
              <Skeleton class="h-3 w-16 rounded" />
            </div>
          </div>
        </div>
      </template>

      <template v-else>
        <div
          v-if="!notificationsStore.notifications.length"
          class="flex flex-col items-center justify-center text-center"
        >
          <p>Oops, there is no notification.</p>
          <Button
            variant="outline"
            size="sm"
            class="mt-2"
            @click="notificationsStore.fetchNotification()"
          >
            Try Again
          </Button>
        </div>

        <RouterLink
          v-for="notification in notificationsStore.notifications"
          :key="notification.notification_id"
          :to="`/articles/${notification.article_id}`"
          class="block"
          @click="handleNotificationClick(notification)"
        >
          <div
            class="flex items-start p-4 border rounded-lg bg-white hover:bg-gray-50 transition-all cursor-pointer"
          >
            <Avatar size="base" class="h-12 w-12 mr-4 flex-shrink-0">
              <AvatarImage :src="notification.user_photo_path || ''" />
              <AvatarFallback class="text-white">
                {{ getInitials(notification.user_name) }}
              </AvatarFallback>
            </Avatar>

            <div class="flex-1 flex justify-between min-w-0">
              <div class="min-w-0">
                <div
                  class="text-base font-semibold text-gray-800 truncate overflow-hidden whitespace-nowrap"
                >
                  From {{ notification.user_name }}
                  <span class="hidden md:inline">
                    ({{ notification.user_type }} - {{ notification.faculty_name }})
                  </span>
                </div>
                <p class="text-sm text-gray-600 mt-1 truncate overflow-hidden whitespace-nowrap">
                  {{ notificationMessage(notification.notification_type) }}
                  - {{ notification.article_title }}
                </p>
              </div>

              <div class="flex flex-col items-end ml-4 flex-shrink-0">
                <div v-if="!notification.seen" class="relative">
                  <span class="absolute top-[-6px] right-0 w-3 h-3 bg-red-500 rounded-full"></span>
                </div>
                <span class="text-xs text-gray-500 whitespace-nowrap">
                  {{ dayjs(notification.created_at).fromNow() }}
                </span>
              </div>
            </div>
          </div>
        </RouterLink>
      </template>
    </div>
  </Layout>
</template>
