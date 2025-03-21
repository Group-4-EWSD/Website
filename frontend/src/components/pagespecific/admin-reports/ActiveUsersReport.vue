<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue'
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Button } from '@/components/ui/button'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

interface User {
  id: number
  name: string
  email: string
  avatar: string
  lastActive: string
  pageViews: number
  sessions: number
  avgSessionDuration: string
}

export default defineComponent({
  components: {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
    Button,
    Tabs,
    TabsContent,
    TabsList,
    TabsTrigger,
    Avatar,
    AvatarFallback,
    AvatarImage,
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
  },
  setup() {
    const activeUsers = ref<User[]>([])
    const isLoading = ref<boolean>(true)
    const activeTab = ref<string>('daily')

    const fetchActiveUsers = async (period: string) => {
      isLoading.value = true
      // Simulate API call
      setTimeout(() => {
        // This would be your actual API call
        // const response = await fetch('/api/analytics/active-users?period=' + period);
        // activeUsers.value = await response.json();

        // Placeholder data
        activeUsers.value = [
          {
            id: 1,
            name: 'Sarah Johnson',
            email: 'sarah@example.com',
            avatar: '',
            lastActive: '12 minutes ago',
            pageViews: 145,
            sessions: 23,
            avgSessionDuration: '15m 32s',
          },
          {
            id: 2,
            name: 'Michael Lee',
            email: 'michael@example.com',
            avatar: '',
            lastActive: '45 minutes ago',
            pageViews: 132,
            sessions: 18,
            avgSessionDuration: '12m 45s',
          },
          {
            id: 3,
            name: 'Emily Wilson',
            email: 'emily@example.com',
            avatar: '',
            lastActive: '1 hour ago',
            pageViews: 128,
            sessions: 15,
            avgSessionDuration: '18m 22s',
          },
          {
            id: 4,
            name: 'David Brown',
            email: 'david@example.com',
            avatar: '',
            lastActive: '2 hours ago',
            pageViews: 112,
            sessions: 14,
            avgSessionDuration: '10m 17s',
          },
          {
            id: 5,
            name: 'Jessica Martinez',
            email: 'jessica@example.com',
            avatar: '',
            lastActive: '3 hours ago',
            pageViews: 98,
            sessions: 12,
            avgSessionDuration: '9m 45s',
          },
          // {
          //   id: 6,
          //   name: 'Thomas Clark',
          //   email: 'thomas@example.com',
          //   avatar: '',
          //   lastActive: '5 hours ago',
          //   pageViews: 85,
          //   sessions: 10,
          //   avgSessionDuration: '11m 33s',
          // },
          // {
          //   id: 7,
          //   name: 'Jennifer Lopez',
          //   email: 'jennifer@example.com',
          //   avatar: '',
          //   lastActive: '1 day ago',
          //   pageViews: 76,
          //   sessions: 8,
          //   avgSessionDuration: '8m 21s',
          // },
          // {
          //   id: 8,
          //   name: 'Robert Taylor',
          //   email: 'robert@example.com',
          //   avatar: '',
          //   lastActive: '1 day ago',
          //   pageViews: 65,
          //   sessions: 7,
          //   avgSessionDuration: '7m 15s',
          // },
        ]
        isLoading.value = false
      }, 500)
    }

    const handleTabChange = (value: string) => {
      activeTab.value = value
      fetchActiveUsers(value)
    }

    onMounted(() => {
      fetchActiveUsers(activeTab.value)
    })

    const getInitials = (name: string): string => {
      return name
        .split(' ')
        .map((part) => part[0])
        .join('')
    }

    return {
      activeUsers,
      isLoading,
      activeTab,
      handleTabChange,
      getInitials,
    }
  },
})
</script>

<template>
  <Card class="w-full">
    <CardHeader>
      <div class="flex items-center justify-between">
        <div class="flex flex-col gap-2">
          <CardTitle>Most Active Users</CardTitle>
          <CardDescription>Users with the highest engagement on your site</CardDescription>
        </div>
        <Select v-model="activeTab" @update:modelValue="handleTabChange">
          <SelectTrigger class="w-[150px]">
            <SelectValue placeholder="Select time range" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="daily">Daily</SelectItem>
            <SelectItem value="weekly">Weekly</SelectItem>
            <SelectItem value="monthly">Montly</SelectItem>
          </SelectContent>
        </Select>
      </div>
    </CardHeader>
    <CardContent>
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>User</TableHead>
            <TableHead>Last Active</TableHead>
            <TableHead class="text-right">Avg. Session</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody v-if="!isLoading">
          <TableRow v-for="user in activeUsers" :key="user.id">
            <TableCell>
              <div class="flex items-center gap-3">
                <Avatar>
                  <AvatarImage :src="user.avatar" />
                  <AvatarFallback>{{ getInitials(user.name) }}</AvatarFallback>
                </Avatar>
                <div>
                  <div class="font-medium">{{ user.name }}</div>
                  <div class="text-sm text-muted-foreground">{{ user.email }}</div>
                </div>
              </div>
            </TableCell>
            <TableCell>{{ user.lastActive }}</TableCell>
            <TableCell class="text-right">{{ user.avgSessionDuration }}</TableCell>
          </TableRow>
        </TableBody>
        <TableBody v-else>
          <TableRow v-for="i in 5" :key="i">
            <TableCell><div class="h-10 bg-gray-200 rounded animate-pulse"></div></TableCell>
            <TableCell><div class="h-5 bg-gray-200 rounded animate-pulse"></div></TableCell>
            <TableCell><div class="h-5 bg-gray-200 rounded animate-pulse"></div></TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </CardContent>
  </Card>
</template>
