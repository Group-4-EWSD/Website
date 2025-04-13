<script setup lang="ts">
import { ArchiveX, CircleCheckBig, FilePenLine, FileText } from 'lucide-vue-next'
import { onMounted } from 'vue'

import ArticleTable from '@/components/pagespecific/coordinator-articles/ArticleTable.vue'
import Card from '@/components/ui/card/Card.vue'
import Layout from '@/components/ui/Layout.vue'
import Skeleton from '@/components/ui/skeleton/Skeleton.vue'
import { useCoordinatorStore } from '@/stores/coordinator'

const coordinatorStore = useCoordinatorStore()

onMounted(() => {
  if (!coordinatorStore.magazineArticles.articles.length) {
    coordinatorStore.fetchMagazineArticles()
  }
})
</script>

<template>
  <Layout>
    <h2 class="text-xl font-bold mb-4 uppercase">Articles</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <Card class="p-5 flex flex-row gap-5 items-center">
        <FileText class="h-14 w-14 text-blue-500" />
        <div class="flex flex-col gap-3">
          <h2 class="font-semibold">Total Submission</h2>

          <Skeleton v-if="false" class="w-20 h-5" />
          <p v-if="true">{{ coordinatorStore.magazineArticles.totalSubmissions }}</p>
        </div>
      </Card>
      <Card class="p-5 flex flex-row gap-5 items-center">
        <FilePenLine class="h-14 w-14 text-yellow-500" />
        <div class="flex flex-col gap-3">
          <h2 class="font-semibold">Pending Review</h2>

          <Skeleton v-if="false" class="w-20 h-5" />
          <p v-if="true">{{ coordinatorStore.magazineArticles.pendingReview }}</p>
        </div>
      </Card>
      <Card class="p-5 flex flex-row gap-5 items-center">
        <CircleCheckBig class="h-14 w-14 text-green-500" />
        <div class="flex flex-col gap-3">
          <h2 class="font-semibold">Approved Articles</h2>

          <Skeleton v-if="false" class="w-20 h-5" />
          <p v-if="true">{{ coordinatorStore.magazineArticles.approvedArticles }}</p>
        </div>
      </Card>
      <Card class="p-5 flex flex-row gap-5 items-center">
        <ArchiveX class="h-14 w-14 text-red-500" />
        <div class="flex flex-col gap-3">
          <h2 class="font-semibold">Rejected Articles</h2>

          <Skeleton v-if="false" class="w-20 h-5" />
          <p v-if="true">{{ coordinatorStore.magazineArticles.rejectedArticles }}</p>
        </div>
      </Card>
    </div>
    <div class="flex flex-col gap-3">
      <h2 class="text-xl font-bold mb-2 mt-4 uppercase">List of Articles</h2>
      <ArticleTable
        :articles="coordinatorStore.magazineArticles.articles"
        :isLoading="coordinatorStore.isLoading"
      />
    </div>
  </Layout>
</template>
