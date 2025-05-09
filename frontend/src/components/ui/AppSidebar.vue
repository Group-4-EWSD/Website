<script setup lang="ts">
import {
  BadgeHelp,
  Bell,
  BellIcon,
  Calendar,
  CircleGauge,
  FileText,
  Home,
  LogOut,
  Settings,
  User,
} from 'lucide-vue-next'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

import {
  Sidebar,
  SidebarContent,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar'
import { forceSignOut } from '@/lib/utils'
import { useUserStore } from '@/stores/user'

import Button from './button/Button.vue'
import Separator from './separator/Separator.vue'

const loading = ref(false)

const userStore = useUserStore()
const userType = userStore.user?.user_type_name
// const userType = 'Marketing Coordinator'
let items = []

switch (userType) {
  case 'Student':
    items = [
      {
        title: 'Home',
        url: '/student/home',
        icon: Home,
      },
      {
        title: 'My Articles',
        url: '/student/my-articles',
        icon: FileText,
      },
      {
        title: 'Notifications',
        url: '/notifications',
        icon: Bell,
      },
      {
        title: 'Settings',
        url: '/settings',
        icon: Settings,
      },
    ]
    break

  case 'Marketing Coordinator':
    items = [
      { title: 'Dashboard', url: '/coordinator/dashboard', icon: CircleGauge },
      {
        title: 'Articles',
        url: '/coordinator/articles',
        icon: FileText,
      },
      {
        title: 'Notifications',
        url: '/notifications',
        icon: Bell,
      },
      {
        title: 'Settings',
        url: '/settings',
        icon: Settings,
      },
    ]
    break

  case 'Marketing Manager':
    items = [
      { title: 'Dashboard', url: '/manager/dashboard', icon: CircleGauge },
      {
        title: 'Articles',
        url: '/manager/articles',
        icon: FileText,
      },
      {
        title: 'Notifications',
        url: '/notifications',
        icon: Bell,
      },
      {
        title: 'Settings',
        url: '/settings',
        icon: Settings,
      },
    ]
    break
  case 'Admin':
    items = [
      { title: 'Management', url: '/admin/management', icon: Calendar },
      {
        title: 'Reports',
        url: '/admin/reports',
        icon: FileText,
      },
      {
        title: 'Users',
        url: '/admin/users',
        icon: User,
      },
      {
        title: 'Contact Messages',
        url: '/admin/contact-us',
        icon: BellIcon,
      },
      {
        title: 'Settings',
        url: '/settings',
        icon: Settings,
      },
    ]
    break
  case 'Guest':
    items = [
      { title: 'Dashboard', url: '/guest/dashboard', icon: CircleGauge },
      {
        title: 'Articles',
        url: '/guest/articles',
        icon: FileText,
      },
      {
        title: 'Settings',
        url: '/settings',
        icon: Settings,
      },
    ]
    break
  default:
    items = [{ title: 'Help', url: '/help', icon: BadgeHelp }]
    break
}

const handleLogout = async () => {
  loading.value = true
  try {
    await forceSignOut(true)
  } catch (error) {
    console.error('Logout failed:', error)
    toast.error('Failed to logout.')
  } finally {
    loading.value = false
  }
}
</script>
<template>
  <Sidebar class="h-[80vh] overflow-y-auto">
    <SidebarContent>
      <SidebarMenu class="flex flex-col h-full">
        <SidebarMenuItem v-for="item in items" :key="item.title">
          <SidebarMenuButton as-child>
            <RouterLink :to="item.url" class="p-6" active-class="bg-primary text-white rounded-lg">
              <component :is="item.icon" />
              <span>{{ item.title }}</span>
            </RouterLink>
          </SidebarMenuButton>
        </SidebarMenuItem>

        <Separator class="mt-auto mb-2" />

        <SidebarMenuItem :key="items[items.length - 1].title">
          <SidebarMenuButton as-child>
            <Button
              class="p-6 bg-white text-primary hover:bg-primary hover:text-white"
              :disabled="loading"
              :processing="loading"
              @click="handleLogout"
            >
              <LogOut />
              <span>{{ loading ? 'Logging out...' : 'Logout' }}</span>
            </Button>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarContent>
  </Sidebar>
</template>
