<script setup lang="ts">
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import { onMounted, ref } from 'vue'

import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import Button from '@/components/ui/button/Button.vue'
import Layout from '@/components/ui/Layout.vue'
import Skeleton from '@/components/ui/skeleton/Skeleton.vue'
import { useNotificationsStore } from '@/stores/notification'

import { getInitials } from '@/lib/utils'

dayjs.extend(relativeTime)

const notificationsStore = useNotificationsStore()

onMounted(() => {
  if (!notificationsStore.notifications.length) notificationsStore.fetchNotification()
})

const handleNotificationClick = (notification: any) => {
  notificationsStore.changeNotiSeen(notification.notification_id)
  notification.seen = 1
}
</script>

<template>
  <Layout>
    <h1 class="text-2xl font-semibold mb-4">Notifications</h1>

    <!-- Notification Cards -->
    <div class="space-y-4">
      <template v-if="notificationsStore.isLoading">
        <div v-for="n in 5" :key="n" class="flex items-start p-4 border rounded-lg bg-white">
          <Skeleton class="w-12 h-12 rounded-full" />

          <div class="flex-1 flex justify-between ml-4">
            <div class="space-y-2">
              <Skeleton class="h-5 w-48 rounded" />

              <Skeleton class="h-4 w-64 rounded" />
            </div>

            <div class="flex flex-col items-end ml-4 flex-shrink-0">
              <Skeleton class="h-3 w-16 rounded" />
            </div>
          </div>
        </div>
      </template>
      <template v-else>
        <div
          v-if="!notificationsStore.notifications.length"
          class="flex flex-col items-center justify-center"
        >
          <p>Oops, there is no notification.</p>
          <Button
            variant="outline"
            size="sm"
            class="ml-2"
            @click="notificationsStore.fetchNotification()"
          >
            Try Again
          </Button>
        </div>
        <RouterLink
          v-else
          v-for="notification in notificationsStore.notifications"
          :key="notification.notification_id"
          :to="`/articles/${notification.article_id}`"
          class="block"
          @click="handleNotificationClick(notification)"
        >
          <div
            class="flex items-start p-4 border rounded-lg bg-white hover:bg-gray-50 transition-all cursor-pointer"
          >
            <Avatar size="base" class="h-12 w-12 mr-4">
              <AvatarImage :src="notification.user_photo_path || ''" />
              <AvatarFallback class="text-white">{{
                getInitials(notification.user_name)
              }}</AvatarFallback>
            </Avatar>

            <div class="flex-1 flex justify-between">
              <!-- Left side -->
              <div>
                <div class="text-lg font-semibold text-gray-800">
                  From {{ notification.user_name }} ({{ notification.user_type }} -
                  {{ notification.faculty_name }})
                </div>
                <p class="text-sm text-gray-600 mt-1">{{ notification.article_title }}</p>
              </div>

              <!-- Right side -->
              <div class="flex flex-col items-end ml-4 flex-shrink-0 relative">
                <div v-if="!notification.seen">
                  <span class="absolute top-[-8px] right-0 w-3 h-3 bg-red-500 rounded-full"></span>
                  <div class="flex-grow"></div>
                </div>

                <span class="text-xs text-gray-500">
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
