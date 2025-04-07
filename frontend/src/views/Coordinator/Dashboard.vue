<script setup lang="ts">
import { ArrowDown, ArrowUp } from 'lucide-vue-next'

import { computed, onMounted, ref } from 'vue'

import ArticleChart from '@/components/pagespecific/coordinator-home/ArticleChart.vue'
import GuestListTable from '@/components/pagespecific/coordinator-home/GuestListTable.vue'
import MagazineArticles from '@/components/pagespecific/coordinator-home/MagazineArticles.vue'
import Badge from '@/components/ui/badge/Badge.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import Layout from '@/components/ui/Layout.vue'
import { useCoordinatorStore } from '@/stores/coordinator'

const daysLeft = ref()
const coordinatorStore = useCoordinatorStore()

onMounted(() => {
  if (!coordinatorStore.articles.length) {
    coordinatorStore.fetchAllArticles()
  }
  daysLeft.value = coordinatorStore.publicDate
})
</script>

<template>
  <Layout>
    <div class="space-y-4">
      <div class="flex items-center justify-between mb-4">
        <!-- Dashboard Title and Previous Login Time Section -->
        <div class="flex flex-col">
          <h2 class="text-xl font-bold uppercase">Coordinator Dashboard</h2>
          <p class="text-sm text-gray-500">Previous Login {{ coordinatorStore.prevLogin }}</p>
        </div>
        <!-- Top Badges -->
        <div class="flex justify-between">
          <Badge variant="secondary" class="px-4 py-2 mr-2 cursor-pointer">IN PROGRESS</Badge>
          <Badge
            variant="outline"
            :class="{
              'bg-red-500 text-white': daysLeft < 10,
              'bg-transparent text-gray-700': daysLeft >= 10,
            }"
            class="px-4 py-2"
            >TOTAL DAYS LEFT UNTIL PUBLICATIONS: {{ daysLeft }} Days</Badge
          >
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Card class="bg-primary text-white md:col-span-2">
          <CardHeader>
            <CardTitle class="font-primary">Articles (Pre Submission)</CardTitle>
          </CardHeader>
          <CardContent class="font-primary grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-3">
            <div class="flex flex-col gap-3">
              <div class="flex items-center justify-between">
                <div class="text-accent">Total Articles</div>
                <div class="text-lg">{{ coordinatorStore.countData?.total_articles }}</div>
              </div>

              <div class="flex items-center justify-between">
                <div class="text-accent">Reviewed Articles</div>
                <div class="text-lg">{{ coordinatorStore.countData?.reviewed_articles }}</div>
              </div>

              <div class="flex items-center justify-between">
                <div class="text-accent">Pending Review</div>
                <div class="text-lg">{{ coordinatorStore.countData?.pending_articles }}</div>
              </div>
            </div>
            <div class="flex flex-col gap-3">
              <div class="flex items-center justify-between">
                <div class="text-accent">Published Articles</div>
                <div class="text-lg">{{ coordinatorStore.countData?.published_articles }}</div>
              </div>

              <div class="flex items-center justify-between">
                <div class="text-accent">Approved</div>
                <div class="text-lg">{{ coordinatorStore.countData?.approved_articles }}</div>
              </div>

              <div class="flex items-center justify-between">
                <div class="text-accent">Rejected</div>
                <div class="text-lg">{{ coordinatorStore.countData?.rejected_articles }}</div>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card class="bg-primary text-white">
          <CardHeader>
            <CardTitle class="font-primary text-lg">Current Academic Year</CardTitle>
          </CardHeader>
          <CardContent class="font-primary flex flex-col gap-3">
            <div class="flex items-center justify-between">
              <div class="text-accent">Participate</div>
              <div class="text-lg">20% <ArrowUp class="inline w-4 h-4 text-green-500" /></div>
            </div>

            <div class="flex items-center justify-between">
              <div class="text-accent">Interest Rate</div>
              <div class="text-lg">10% <ArrowUp class="inline w-4 h-4 text-green-500" /></div>
            </div>

            <div class="flex items-center justify-between">
              <div class="text-accent">In Time Rate</div>
              <div class="text-lg">10% <ArrowDown class="inline w-4 h-4 text-red-500" /></div>
            </div>
          </CardContent>
        </Card>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <ArticleChart />
          <GuestListTable
            :guests="coordinatorStore.guestList"
            :isLoading="coordinatorStore.isLoading"
            class="mt-6"
          />
        </div>

        <MagazineArticles
          :articles="coordinatorStore.articles"
          :isLoading="coordinatorStore.isLoading"
        />
      </div>
    </div>
  </Layout>
</template>
