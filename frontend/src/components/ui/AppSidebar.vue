<script setup lang="ts">
import { FileText, Home, Bell, LogOut, Settings } from 'lucide-vue-next'

import {
  Sidebar,
  SidebarContent,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar'

import Separator from './separator/Separator.vue'

const items = [
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
    url: '/student/notifications',
    icon: Bell,
  },
  {
    title: 'Settings',
    url: '/student/settings',
    icon: Settings,
  },
  {
    title: 'Log Out',
    url: '#',
    icon: LogOut,
  },
]
</script>
<template>
  <Sidebar class="h-[80vh] overflow-y-auto">
    <SidebarContent>
      <SidebarMenu class="flex flex-col h-full">
        <SidebarMenuItem v-for="item in items.slice(0, -1)" :key="item.title">
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
            <a :href="items[items.length - 1].url" class="p-6">
              <component :is="items[items.length - 1].icon" />
              <span>{{ items[items.length - 1].title }}</span>
            </a>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarContent>
  </Sidebar>
</template>
