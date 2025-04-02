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

const articleStats = [
  { label1: 'Total Articles', value1: '132 nos', label2: 'Published Articles', value2: '100 nos' },
  { label1: 'Reviewed Articles', value1: '32 nos', label2: 'Approved', value2: '100 nos' },
  { label1: 'Pending Review', value1: '100 nos', label2: 'Reject', value2: '32 nos' },
]

onMounted(() => {
  if (!coordinatorStore.articles.length) {
    coordinatorStore.fetchArticles()
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
        <Card class="bg-primary text-white">
          <CardHeader>
            <CardTitle class="font-primary">Articles (Pre Submission)</CardTitle>
          </CardHeader>
          <CardContent class="font-primary flex flex-row">
            <div class="space-y-2">
              <p>
                Total Articles: <strong>{{ coordinatorStore.countData?.total_articles }}</strong>
              </p>
              <p>
                Reviewed Articles:
                <strong>{{ coordinatorStore.countData?.reviewed_articles }}</strong>
              </p>
              <p>
                Pending Review: <strong>{{ coordinatorStore.countData?.pending_articles }}</strong>
              </p>
            </div>
            <div class="space-y-2 ml-10">
              <p>
                Total Articles:
                <strong>{{ coordinatorStore.countData?.published_articles }}</strong>
              </p>
              <p>
                Approved: <strong>{{ coordinatorStore.countData?.approved_articles }}</strong>
              </p>
              <p>
                Reject: <strong>{{ coordinatorStore.countData?.rejected_articles }}</strong>
              </p>
            </div>
          </CardContent>
        </Card>

        <Card class="bg-primary text-white">
          <CardHeader>
            <CardTitle class="font-primary">Current Academic Year</CardTitle>
          </CardHeader>
          <CardContent class="font-primary space-y-2">
            <p>
              Participate Rate: <strong>20%</strong>
              <ArrowUp class="inline w-4 h-4 text-green-500" />
            </p>
            <p>
              Interest Rate: <strong>10%</strong>
              <ArrowUp class="inline w-4 h-4 text-green-500" />
            </p>
            <p>
              In Time Rate: <strong>5%</strong>
              <ArrowDown class="inline w-4 h-4 text-red-500" />
            </p>
          </CardContent>
        </Card>

        <Card class="text-primary border-grey-500">
          <CardHeader>
            <CardTitle class="font-primary">Current Academic Year</CardTitle>
          </CardHeader>
          <CardContent class="font-primary space-y-2">
            <p>
              Participate Rate: <strong>20%</strong>
              <ArrowUp class="inline w-4 h-4 text-green-500" />
            </p>
            <p>
              Interest Rate: <strong>10%</strong>
              <ArrowUp class="inline w-4 h-4 text-green-500" />
            </p>
            <p>
              In Time Rate: <strong>5%</strong>
              <ArrowDown class="inline w-4 h-4 text-red-500" />
            </p>
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
