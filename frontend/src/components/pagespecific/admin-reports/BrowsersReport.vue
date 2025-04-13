<script lang="ts">
import { computed, defineComponent, type PropType } from 'vue'

import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'

interface Browser {
  browser_id: number;
  browser_name: string;
  login_count: number;
}

export default defineComponent({
  components: {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
    Separator,
  },
  props: {
    data: {
      type: Array as PropType<Browser[]>,
      required: true,
      default: () => []
    },
    isLoading: {
      type: Boolean,
      default: false
    },
    totalUsers: {
      type: Number,
      default: 0
    }
  },
  setup(props) {
    // Computed property to check if mobile view
    const isMobile = computed(() => {
      return window.innerWidth < 640;
    });

    return {
      isMobile
    }
  },
})
</script>

<template>
  <Card class="w-full">
    <CardHeader>
      <div class="flex items-center justify-between">
        <div class="flex flex-col gap-2">
          <CardTitle>Browser Usage</CardTitle>
          <CardDescription>Distribution of browsers used to access your site</CardDescription>
        </div>
      </div>
    </CardHeader>
    <CardContent>
      <div v-if="!isLoading">
        <!-- <div class="text-sm text-muted-foreground mb-4">
          Total users: {{ totalUsers.toLocaleString() }}
        </div> -->
        
        <!-- Desktop View -->
        <div v-if="!isMobile" class="space-y-4">
          <div v-for="browser in data" :key="browser.browser_id" class="space-y-2">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="font-medium">{{ browser.browser_name }}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="font-semibold">{{ browser.login_count.toLocaleString() }} users</span>
              </div>
            </div>
            <Separator v-if="browser.browser_id !== data[data.length - 1].browser_id" class="mt-4" />
          </div>
        </div>
        
        <!-- Mobile View -->
        <div v-else class="space-y-4">
          <div v-for="browser in data" :key="browser.browser_id" class="space-y-2">
            <div class="flex flex-col">
              <span class="font-medium">{{ browser.browser_name }}</span>
              <span class="font-semibold">{{ browser.login_count.toLocaleString() }} users</span>
            </div>
            <Separator v-if="browser.browser_id !== data[data.length - 1].browser_id" class="mt-4" />
          </div>
        </div>
      </div>
      
      <!-- Loading skeleton -->
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
  </Card>
</template>