<!-- PageViewsReport.vue -->
<script lang="ts">
import { defineComponent, onMounted, ref, computed, watch } from 'vue'

import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'

// Most Viewed Pages
interface MostViewedPage {
  page_id: string;
  app_page_name: string;
  view_count: number;
}

// Define the props for the component
interface Props {
  data?: MostViewedPage[];
  isLoading: boolean;
  title?: string;
  description?: string;
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
  },
  props: {
    data: {
      type: Array as () => MostViewedPage[],
      default: () => [],
    },
    isLoading: {
      type: Boolean,
      default: false,
    },
    title: {
      type: String,
      default: 'Most Viewed Pages',
    },
    description: {
      type: String,
      default: 'See which pages are attracting the most traffic',
    },
  },
  setup(props: Props) {
    const pageViews = ref<MostViewedPage[]>([])
    const isLoading = ref<boolean>(props.isLoading || false)
    const isMobileView = ref<boolean>(false)

    // Watch for changes in the data prop
    watch(
      () => props.data,
      (newData) => {
        if (newData) {
          pageViews.value = newData
        }
      },
      { immediate: true, deep: true }
    )

    // Watch for changes in the isLoading prop
    watch(
      () => props.isLoading,
      (newLoading) => {
        isLoading.value = newLoading
      },
      { immediate: true }
    )

    // Check if the screen is mobile size
    const checkMobileView = () => {
      isMobileView.value = window.innerWidth < 640
    }

    onMounted(() => {
      checkMobileView()
      window.addEventListener('resize', checkMobileView)
    })

    return {
      pageViews,
      isLoading,
      isMobileView,
      title: props.title,
      description: props.description,
    }
  },
})
</script>

<template>
  <Card class="w-full">
    <CardHeader>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex flex-col gap-2">
          <CardTitle>{{ title }}</CardTitle>
          <CardDescription>{{ description }}</CardDescription>
        </div>
      </div>
    </CardHeader>
    <CardContent>
      <!-- Desktop Table View (Hidden on Mobile) -->
      <div class="hidden sm:block overflow-x-auto">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead class="whitespace-nowrap">Page</TableHead>
              <TableHead class="text-right whitespace-nowrap">Views</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody v-if="!isLoading">
            <TableRow v-for="page in pageViews" :key="page.page_id">
              <TableCell class="font-medium">{{ page.app_page_name }}</TableCell>
              <TableCell class="text-right">{{ page.view_count.toLocaleString() }}</TableCell>
            </TableRow>
          </TableBody>
          <TableBody v-else>
            <TableRow v-for="i in 5" :key="i">
              <TableCell><div class="h-5 bg-gray-200 rounded animate-pulse"></div></TableCell>
              <TableCell><div class="h-5 bg-gray-200 rounded animate-pulse"></div></TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <!-- Mobile Card View (Visible only on Mobile) -->
      <div class="block sm:hidden">
        <div v-if="!isLoading" class="space-y-2">
          <div
            v-for="page in pageViews"
            :key="page.page_id"
            class="border rounded-lg p-3 flex items-center justify-between"
          >
            <div class="min-w-0 flex-1">
              <h3 class="font-medium text-sm truncate">{{ page.app_page_name }}</h3>
            </div>
            <span class="text-sm font-medium text-gray-900 ml-2 whitespace-nowrap">
              {{ page.view_count.toLocaleString() }}
            </span>
          </div>
        </div>
        <div v-else class="space-y-2">
          <div v-for="i in 5" :key="i" class="border rounded-lg p-3 flex items-center">
            <div class="flex-1">
              <div class="h-4 bg-gray-200 rounded animate-pulse mb-1 w-2/3"></div>
            </div>
            <div class="h-4 bg-gray-200 rounded animate-pulse w-12 ml-2"></div>
          </div>
        </div>
      </div>
    </CardContent>
  </Card>
</template>