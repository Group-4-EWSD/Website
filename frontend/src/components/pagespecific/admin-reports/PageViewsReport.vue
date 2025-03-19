<!-- PageViewsReport.vue -->
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
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

interface PageView {
  id: number
  path: string
  title: string
  views: number
  uniqueVisitors: number
  avgTimeOnPage: string
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
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
  },
  setup() {
    const pageViews = ref<PageView[]>([])
    const isLoading = ref<boolean>(true)
    const timeRange = ref<string>('7d')

    const fetchPageViews = async (range: string) => {
      isLoading.value = true
      // Simulate API call
      setTimeout(() => {
        // This would be your actual API call
        // const response = await fetch('/api/analytics/page-views?range=' + range);
        // pageViews.value = await response.json();

        // Placeholder data
        pageViews.value = [
          {
            id: 1,
            path: '/',
            title: 'Home Page',
            views: 15423,
            uniqueVisitors: 10234,
            avgTimeOnPage: '2m 45s',
          },
          {
            id: 2,
            path: '/products',
            title: 'Products',
            views: 12543,
            uniqueVisitors: 8645,
            avgTimeOnPage: '3m 12s',
          },
          {
            id: 3,
            path: '/blog',
            title: 'Blog',
            views: 9876,
            uniqueVisitors: 7123,
            avgTimeOnPage: '4m 32s',
          },
          {
            id: 4,
            path: '/about',
            title: 'About Us',
            views: 7654,
            uniqueVisitors: 6543,
            avgTimeOnPage: '1m 47s',
          },
          {
            id: 5,
            path: '/contact',
            title: 'Contact',
            views: 5432,
            uniqueVisitors: 4321,
            avgTimeOnPage: '1m 23s',
          },
          {
            id: 6,
            path: '/blog/top-10-tips',
            title: 'Top 10 Tips',
            views: 4567,
            uniqueVisitors: 3456,
            avgTimeOnPage: '5m 11s',
          },
          {
            id: 7,
            path: '/products/featured',
            title: 'Featured Products',
            views: 3456,
            uniqueVisitors: 2345,
            avgTimeOnPage: '3m 56s',
          },
          {
            id: 8,
            path: '/login',
            title: 'Login Page',
            views: 2345,
            uniqueVisitors: 2300,
            avgTimeOnPage: '0m 45s',
          },
        ]
        isLoading.value = false
      }, 500)
    }

    const handleRangeChange = (value: string) => {
      timeRange.value = value
      fetchPageViews(value)
    }

    onMounted(() => {
      fetchPageViews(timeRange.value)
    })

    return {
      pageViews,
      isLoading,
      timeRange,
      handleRangeChange,
    }
  },
})
</script>

<template>
  <Card class="w-full">
    <CardHeader>
      <div class="flex items-center justify-between">
        <div>
          <CardTitle>Most Viewed Pages</CardTitle>
          <CardDescription>See which pages are attracting the most traffic</CardDescription>
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
      <Table>
        <TableCaption>Page views data for the selected time period.</TableCaption>
        <TableHeader>
          <TableRow>
            <TableHead>Page</TableHead>
            <TableHead>Path</TableHead>
            <TableHead class="text-right">Views</TableHead>
            <TableHead class="text-right">Unique Visitors</TableHead>
            <TableHead class="text-right">Avg. Time on Page</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody v-if="!isLoading">
          <TableRow v-for="page in pageViews" :key="page.id">
            <TableCell class="font-medium">{{ page.title }}</TableCell>
            <TableCell>{{ page.path }}</TableCell>
            <TableCell class="text-right">{{ page.views.toLocaleString() }}</TableCell>
            <TableCell class="text-right">{{ page.uniqueVisitors.toLocaleString() }}</TableCell>
            <TableCell class="text-right">{{ page.avgTimeOnPage }}</TableCell>
          </TableRow>
        </TableBody>
        <TableBody v-else>
          <TableRow v-for="i in 5" :key="i">
            <TableCell><div class="h-5 bg-gray-200 rounded animate-pulse"></div></TableCell>
            <TableCell><div class="h-5 bg-gray-200 rounded animate-pulse"></div></TableCell>
            <TableCell><div class="h-5 bg-gray-200 rounded animate-pulse"></div></TableCell>
            <TableCell><div class="h-5 bg-gray-200 rounded animate-pulse"></div></TableCell>
            <TableCell><div class="h-5 bg-gray-200 rounded animate-pulse"></div></TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </CardContent>
  </Card>
</template>
