<script setup lang="ts">
import Layout from '@/components/ui/Layout.vue'
import ArticleChart from '@/components/pagespecific/coordinator-home/ArticleChart.vue'
import GuestListTable from '@/components/pagespecific/coordinator-home/GuestListTable.vue'
import MagazineArticles from '@/components/pagespecific/coordinator-home/MagazineArticles.vue'

import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import Badge from '@/components/ui/badge/Badge.vue'
import { ArrowUp, ArrowDown } from 'lucide-vue-next'
import { ref } from 'vue'

const daysLeft = ref(4)

const articleStats = [
  { label1: 'Total Articles', value1: '132 nos', label2: 'Published Articles', value2: '100 nos' },
  { label1: 'Reviewed Articles', value1: '32 nos', label2: 'Approved', value2: '100 nos' },
  { label1: 'Pending Review', value1: '100 nos', label2: 'Reject', value2: '32 nos' },
]
</script>

<template>
  <Layout>
    <div class="space-y-4">
      <div class="flex items-center justify-between mb-4">
        <!-- Dashboard Title and Previous Login Time Section -->
        <div class="flex flex-col">
          <h2 class="text-xl font-bold uppercase">Coordinator Dashboard</h2>
          <p class="text-sm text-gray-500">Previous Login 09/03/2025 9:20 AM</p>
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
          <CardContent class="font-primary space-y-2">
            <p>Total Articles: <strong>132</strong></p>
            <p>Review Articles: <strong>100</strong></p>
            <p>Under Review: <strong>32</strong></p>
          </CardContent>
        </Card>

        <Card class="bg-primary text-white">
          <CardHeader>
            <CardTitle class="font-primary">Articles (Final)</CardTitle>
          </CardHeader>
          <CardContent class="font-primary space-y-2">
            <p>Total Articles: <strong>132</strong></p>
            <p>Approved: <strong>100</strong></p>
            <p>Reject: <strong>32</strong></p>
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
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <ArticleChart />
          <GuestListTable class="mt-6" />
        </div>

        <MagazineArticles />
      </div>
    </div>
  </Layout>
</template>
