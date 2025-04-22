<script setup lang="ts">
import { ArrowDown, ArrowUp } from 'lucide-vue-next'
import { onMounted } from 'vue'

import GuestListTable from '@/components/pagespecific/coordinator-home/GuestListTable.vue'
import AuroraMembers from '@/components/pagespecific/manager-dashboard/AuroraMembers.vue'
import ArticleChart from '@/components/shared/ArticleChart.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import Layout from '@/components/ui/Layout.vue'
import { useManagerStore } from '@/stores/manager'


const managerStore = useManagerStore()

onMounted(() => {
  managerStore.fetchDashboardData()
  console.log(managerStore.chartData)
})
</script>

<template>
  <Layout>
    <div class="space-y-4">
      <div class="flex items-center justify-between mb-4">
        <!-- Dashboard Title and Previous Login Time Section -->
        <div class="flex flex-col">
          <h2 class="text-xl font-bold uppercase">Manager Dashboard</h2>
          <p class="text-sm text-gray-500">Previous Login {{ managerStore.prevLogin }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <Card class="bg-primary text-white md:col-span-2">
          <CardHeader>
            <CardTitle class="font-primary text-lg">Articles</CardTitle>
          </CardHeader>
          <CardContent class="font-primary grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-3">
            <div class="flex flex-col gap-3">
              <div class="flex items-center justify-between">
                <div class="text-accent">Total Articles</div>
                <div class="text-lg">{{ managerStore.countData?.total_articles }}</div>
              </div>

              <div class="flex items-center justify-between">
                <div class="text-accent">Review Articles</div>
                <div class="text-lg">{{ managerStore.countData?.reviewed_articles }}</div>
              </div>

              <div class="flex items-center justify-between">
                <div class="text-accent">Pending Review</div>
                <div class="text-lg">{{ managerStore.countData?.pending_articles }}</div>
              </div>
            </div>
            <div class="flex flex-col gap-3">
              <div class="flex items-center justify-between">
                <div class="text-accent">Published Articles</div>
                <div class="text-lg">{{ managerStore.countData?.published_articles }}</div>
              </div>

              <div class="flex items-center justify-between">
                <div class="text-accent">Approved</div>
                <div class="text-lg">{{ managerStore.countData?.approved_articles }}</div>
              </div>

              <div class="flex items-center justify-between">
                <div class="text-accent">Rejected</div>
                <div class="text-lg">{{ managerStore.countData?.rejected_articles }}</div>
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
              <div class="text-lg">
                {{ managerStore.countData?.deri_participate_rate }}
                <ArrowUp class="inline w-4 h-4 text-green-500" />
              </div>
            </div>

            <div class="flex items-center justify-between">
              <div class="text-accent">Active Rate</div>
              <div class="text-lg">
                {{ managerStore.countData?.deriActiveUser }}
                <ArrowUp class="inline w-4 h-4 text-green-500" />
              </div>
            </div>

            <div class="flex items-center justify-between">
              <div class="text-accent">In Time Rate</div>
              <div class="text-lg">10% <ArrowDown class="inline w-4 h-4 text-red-500" /></div>
            </div>
          </CardContent>
        </Card>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <ArticleChart :data="managerStore.chartData" class="h-full" />

        <GuestListTable
          :guests="managerStore.guestList"
          :isLoading="managerStore.isLoading"
          class="h-full"
        />
      </div>
      <div>
        <AuroraMembers :members="managerStore.members" :isLoading="managerStore.isLoading" />
      </div>
    </div>
  </Layout>
</template>
