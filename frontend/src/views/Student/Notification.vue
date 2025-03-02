<script setup lang="ts">
import Layout from '@/components/ui/Layout.vue'
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'

dayjs.extend(relativeTime)

const notifications = [
  {
    id: 1,
    name: 'Jean Philippe A.',
    role: 'Student - Art & Science',
    message:
      'Hey, just wanted to remind you about the upcoming project submission deadline. Let me know if you have any questions!',
    profileImg: '@/assets/profile.png',
    dateTime: '2025-03-01 14:30',
  },
  {
    id: 2,
    name: 'Emily Clark',
    role: 'Professor - Computer Science',
    message:
      'Your research paper has been reviewed. Please check the feedback provided in the document.',
    profileImg: '',
    dateTime: '2025-03-01 15:45',
  },
]
</script>

<template>
  <Layout>
    <div class="p-6">
      <h1 class="text-2xl font-semibold mb-4">Notifications</h1>

      <!-- Notification Cards -->
      <div class="space-y-4">
        <div
          v-for="notification in notifications"
          :key="notification.id"
          class="flex items-start p-4 border rounded-lg shadow-md bg-white hover:bg-gray-50 transition-all"
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
                From {{ notification.name }} ({{ notification.role }})
              </div>
              <p class="text-sm text-gray-600 mt-1">{{ notification.message }}</p>
            </div>

            <!-- Right side -->
            <div class="text-xs text-gray-500 ml-4 flex-shrink-0">
              {{ dayjs(notification.dateTime).fromNow() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>
