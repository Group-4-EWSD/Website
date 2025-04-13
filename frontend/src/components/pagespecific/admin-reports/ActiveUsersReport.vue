<script lang="ts">
import { computed, defineComponent, type PropType } from 'vue'

import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
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

interface User {
  id: string;
  user_name: string;
  nickname: string;
  user_email: string;
  gender: number;
  user_type_id: string;
  user_type_name: string;
  faculty_id: string;
  faculty_name: string;
  phone_number: string;
  user_photo_path: string | null;
  date_of_birth: string | null;
  article_count: number;
  action_count: number;
  comment_count: number;
  total_score: number;
}

export default defineComponent({
  components: {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
    Button,
    Avatar,
    AvatarFallback,
    AvatarImage,
  },
  props: {
    data: {
      type: Array as PropType<User[]>,
      required: true,
      default: () => []
    },
    isLoading: {
      type: Boolean,
      required: true,
      default: false
    }
  },
  setup(props) {
    // Computed property to derive activity status based on timestamp
    // const getActivityStatus = (user: User) => {

    //   const activityScore = user.action_count + user.comment_count;
    //   if (activityScore > 100) return 'Active now';
    //   if (activityScore > 50) return '30 minutes ago';
    //   if (activityScore > 20) return '1 hour ago';
    //   if (activityScore > 10) return '3 hours ago';
    //   return 'Today';
    // };

    // Compute average session duration based on user activity
    const getAvgSessionDuration = (user: User) => {
      const base = Math.floor((user.action_count * 30 + user.comment_count * 90) / (user.action_count + user.comment_count || 1));
      const minutes = Math.floor(base / 60);
      const seconds = base % 60;
      return `${minutes}m ${seconds}s`;
    };

    const getInitials = (name: string): string => {
      return name
        .split(' ')
        .map((part) => part[0])
        .join('');
    };

    // Expose functions and data to template
    return {
      getInitials,
      // getActivityStatus,
      getAvgSessionDuration,
    };
  },
})
</script>

<template>
  <Card class="w-full">
    <CardHeader>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex flex-col gap-2">
          <CardTitle>Most Active Users</CardTitle>
          <CardDescription>Users with the highest engagement on your site</CardDescription>
        </div>
      </div>
    </CardHeader>
    <CardContent>
      <!-- Desktop Table View -->
      <div class="hidden sm:block">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>User</TableHead>
              <!-- <TableHead>Last Active</TableHead> -->
              <TableHead class="text-right">Avg. Session</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody v-if="!isLoading">
            <TableRow v-for="user in data" :key="user.id">
              <TableCell>
                <div class="flex items-center gap-3">
                  <Avatar>
                    <AvatarImage :src="user.user_photo_path || ''" />
                    <AvatarFallback>{{ getInitials(user.user_name) }}</AvatarFallback>
                  </Avatar>
                  <div>
                    <div class="font-medium">{{ user.user_name }}</div>
                    <div class="text-sm text-muted-foreground">{{ user.user_email }}</div>
                  </div>
                </div>
              </TableCell>
              <TableCell class="text-right">{{ getAvgSessionDuration(user) }}</TableCell>
            </TableRow>
          </TableBody>
          <TableBody v-else>
            <TableRow v-for="i in 5" :key="i">
              <TableCell><div class="h-10 bg-gray-200 rounded animate-pulse"></div></TableCell>
              <TableCell><div class="h-5 bg-gray-200 rounded animate-pulse"></div></TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <!-- Improved Mobile Card View -->
      <div class="block sm:hidden">
        <div v-if="!isLoading" class="space-y-4">
          <div
            v-for="user in data"
            :key="user.id"
            class="border rounded-lg p-4 shadow-sm bg-card hover:bg-accent/5 transition-colors"
          >
            <div class="flex items-center gap-4 mb-3">
              <Avatar class="h-12 w-12 border-2 border-background">
                <AvatarImage :src="user.user_photo_path || ''" />
                <AvatarFallback class="text-lg font-medium bg-primary/10 text-primary">{{ getInitials(user.user_name) }}</AvatarFallback>
              </Avatar>
              <div class="min-w-0 flex-1">
                <div class="font-medium text-base truncate">{{ user.user_name }}</div>
                <div class="text-sm text-muted-foreground truncate">{{ user.user_email }}</div>
              </div>
            </div>
            <div class="grid grid-cols-1 gap-3 border-t pt-3 text-sm">
              <!-- <div class="flex flex-col">
                <span class="text-xs text-muted-foreground font-medium uppercase tracking-wide">Last Active</span>
                <span class="font-medium mt-1">{{ getActivityStatus(user) }}</span>
              </div> -->
              <div class="flex flex-row items-center justify-between">
                <span class="text-xs text-muted-foreground font-medium uppercase tracking-wide">Avg. Session</span>
                <span class="font-medium mt-1">{{ getAvgSessionDuration(user) }}</span>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="space-y-4">
          <div v-for="i in 5" :key="i" class="border rounded-lg p-4 shadow-sm">
            <div class="flex items-center gap-4 mb-3">
              <div class="h-12 w-12 bg-gray-200 rounded-full animate-pulse"></div>
              <div class="flex-1">
                <div class="h-5 bg-gray-200 rounded animate-pulse w-3/4 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded animate-pulse w-1/2"></div>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-3 border-t pt-3">
              <div>
                <div class="h-3 bg-gray-200 rounded animate-pulse w-1/2 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded animate-pulse w-2/3"></div>
              </div>
              <div class="flex flex-col items-end">
                <div class="h-3 bg-gray-200 rounded animate-pulse w-1/2 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded animate-pulse w-2/3"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </CardContent>
  </Card>
</template>