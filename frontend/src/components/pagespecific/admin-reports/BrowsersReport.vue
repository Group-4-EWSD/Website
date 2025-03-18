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
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Progress } from '@/components/ui/progress'
import { Separator } from '@/components/ui/separator'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

interface Browser {
  id: number
  name: string
  version: string
  icon: string
  percentage: number
  count: number
  trend: 'up' | 'down' | 'stable'
  change: number
}

export default defineComponent({
  components: {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
    Badge,
    Button,
    Progress,
    Separator,
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
  },
  setup() {
    const browsers = ref<Browser[]>([])
    const isLoading = ref<boolean>(true)
    const timeRange = ref<string>('7d')
    const totalUsers = ref<number>(0)

    const fetchBrowserData = async (range: string) => {
      isLoading.value = true
      // Simulate API call
      setTimeout(() => {
        // This would be your actual API call
        // const response = await fetch('/api/analytics/browsers?range=' + range);
        // const data = await response.json();
        // browsers.value = data.browsers;
        // totalUsers.value = data.totalUsers;

        // Placeholder data
        browsers.value = [
          {
            id: 1,
            name: 'Chrome',
            version: '119.0',
            icon: '🌐',
            percentage: 64.5,
            count: 32250,
            trend: 'up',
            change: 2.3,
          },
          {
            id: 2,
            name: 'Safari',
            version: '16.0',
            icon: '🧭',
            percentage: 18.2,
            count: 9100,
            trend: 'down',
            change: 1.5,
          },
          {
            id: 3,
            name: 'Firefox',
            version: '115.0',
            icon: '🦊',
            percentage: 7.8,
            count: 3900,
            trend: 'stable',
            change: 0.2,
          },
          {
            id: 4,
            name: 'Edge',
            version: '118.0',
            icon: '🔷',
            percentage: 5.3,
            count: 2650,
            trend: 'up',
            change: 0.8,
          },
          {
            id: 5,
            name: 'Opera',
            version: '103.0',
            icon: '🔴',
            percentage: 2.1,
            count: 1050,
            trend: 'down',
            change: 0.4,
          },
          {
            id: 6,
            name: 'Samsung Internet',
            version: '21.0',
            icon: '📱',
            percentage: 1.6,
            count: 800,
            trend: 'up',
            change: 0.3,
          },
          {
            id: 7,
            name: 'Others',
            version: '-',
            icon: '🔍',
            percentage: 0.5,
            count: 250,
            trend: 'stable',
            change: 0.0,
          },
        ]
        totalUsers.value = 50000
        isLoading.value = false
      }, 500)
    }

    const handleRangeChange = (value: string) => {
      timeRange.value = value
      fetchBrowserData(value)
    }

    const getTrendColor = (trend: string): string => {
      if (trend === 'up') return 'text-green-500'
      if (trend === 'down') return 'text-red-500'
      return 'text-gray-500'
    }

    const getTrendIcon = (trend: string): string => {
      if (trend === 'up') return '↑'
      if (trend === 'down') return '↓'
      return '→'
    }

    onMounted(() => {
      fetchBrowserData(timeRange.value)
    })

    return {
      browsers,
      isLoading,
      timeRange,
      totalUsers,
      handleRangeChange,
      getTrendColor,
      getTrendIcon,
    }
  },
})
</script>

<template>
  <Card class="w-full">
    <CardHeader>
      <div class="flex items-center justify-between">
        <div>
          <CardTitle>Browser Usage</CardTitle>
          <CardDescription>Distribution of browsers used to access your site</CardDescription>
        </div>
        <Select v-model="timeRange" @update:modelValue="handleRangeChange">
          <SelectTrigger class="w-[150px]">
            <SelectValue placeholder="Select time range" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="7d">Last 7 days</SelectItem>
            <SelectItem value="30d">Last 30 days</SelectItem>
            <SelectItem value="90d">Last quarter</SelectItem>
            <SelectItem value="365d">Last year</SelectItem>
          </SelectContent>
        </Select>
      </div>
    </CardHeader>
    <CardContent>
      <div v-if="!isLoading">
        <div class="text-sm text-muted-foreground mb-4">
          Total users: {{ totalUsers.toLocaleString() }}
        </div>
        <div class="space-y-4">
          <div v-for="browser in browsers" :key="browser.id" class="space-y-2">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="text-lg">{{ browser.icon }}</span>
                <span class="font-medium">{{ browser.name }}</span>
                <Badge variant="outline" class="ml-2">v{{ browser.version }}</Badge>
              </div>
              <div class="flex items-center gap-2">
                <span class="font-semibold">{{ browser.percentage.toFixed(1) }}%</span>
                <span :class="getTrendColor(browser.trend)">
                  {{ getTrendIcon(browser.trend) }} {{ browser.change.toFixed(1) }}%
                </span>
              </div>
            </div>
            <Progress :value="browser.percentage" class="h-2" />
            <div class="text-xs text-muted-foreground">
              {{ browser.count.toLocaleString() }} users
            </div>
            <Separator v-if="browser.id !== browsers.length" class="mt-4" />
          </div>
        </div>
      </div>
      <div v-else class="space-y-4">
        <div v-for="i in 5" :key="i" class="space-y-2">
          <div class="flex justify-between">
            <div class="h-6 w-24 bg-gray-200 rounded animate-pulse"></div>
            <div class="h-6 w-16 bg-gray-200 rounded animate-pulse"></div>
          </div>
          <div class="h-2 bg-gray-200 rounded animate-pulse"></div>
          <div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div>
          <Separator v-if="i !== 5" class="mt-4" />
        </div>
      </div>
    </CardContent>
    <CardFooter>
      <Button variant="outline" size="sm">Export Data</Button>
    </CardFooter>
  </Card>
</template>
