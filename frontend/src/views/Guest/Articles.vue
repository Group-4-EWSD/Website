<script setup lang="ts">
import {
  Select,
  SelectItem,
  SelectValue,
  SelectTrigger,
  SelectContent,
} from '@/components/ui/select'
import Layout from '@/components/ui/Layout.vue'
import ArticleTable from '@/components/pagespecific/coordinator-articles/ArticleTable.vue'
import { useGuestStore } from '@/stores/guest'
import { onMounted } from 'vue'

const guestStore = useGuestStore()

onMounted(() => {
  if (!guestStore.articles.length) {
    guestStore.fetchDashboardData()
  }
})
</script>

<template>
  <Layout>
    <div class="flex flex-col gap-3">
      <div class="flex justify-between items-center mt-4 mb-2">
        <h2 class="text-xl font-bold uppercase">Articles</h2>

        <div class="flex items-center gap-4">
          <!-- Feedback Filter -->
          <Select>
            <SelectTrigger class="w-[180px]">
              <SelectValue placeholder="Filter by feedback" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All</SelectItem>
              <SelectItem value="given">Feedback Given</SelectItem>
              <SelectItem value="not_given">Not Given</SelectItem>
              <SelectItem value="within_14_days">Given Within 14 Days</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>

      <ArticleTable :articles="guestStore.articles" :isLoading="guestStore.isLoading" />
    </div>
  </Layout>
</template>
