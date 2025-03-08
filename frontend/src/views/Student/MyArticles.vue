<script setup lang="ts">
import { CalendarDays } from 'lucide-vue-next'
import { onMounted } from 'vue'
import ArticlePost from '@/components/pagespecific/my-articles/ArticlePost.vue'
import LatestArticles from '@/components/pagespecific/my-articles/LatestArticles.vue'
import UploadArticle from '@/components/pagespecific/my-articles/UploadArticle.vue'
import { Button } from '@/components/ui/button'
import { Card } from '@/components/ui/card'
import Layout from '@/components/ui/Layout.vue'
import { Pagination } from '@/components/ui/pagination'
import { Skeleton } from '@/components/ui/skeleton'
import { useMyArticlesStore } from '@/stores/my-articles'

const myArticlesStore = useMyArticlesStore()

onMounted(() => {
  // Only fetch if we haven't loaded data yet
  myArticlesStore.fetchArticles()
})

// Format date for display
const formatDate = (dateString: string): string => {
  const date = new Date(dateString)
  const options = { year: 'numeric' as const, month: 'short' as const, day: 'numeric' as const }
  return date.toLocaleDateString('en-US', options)
}
</script>

<template>
  <Layout>
    <h2 class="text-xl font-bold mb-4 uppercase">My Articles</h2>
    <div class="flex flex-col gap-5">
      <div class="grid md:grid-cols-3 grid-cols-1 gap-5">
        <Card class="p-5 flex flex-col gap-5">
          <h2 class="font-semibold uppercase">Upload Articles</h2>
          <div class="grid md:grid-cols-2 grid-cols-1 gap-3">
            <UploadArticle />
            <RouterLink to="/student/my-articles/draft">
              <Button variant="secondary" class="uppercase w-full">View draft</Button>
            </RouterLink>
          </div>
        </Card>
        <Card class="p-5 flex flex-row gap-5 items-center">
          <CalendarDays class="h-14 w-14 text-yellow-500" />
          <div class="flex flex-col gap-3">
            <h2 class="font-semibold">Pre Upload Deadline</h2>

            <Skeleton v-if="myArticlesStore.isLoading && !myArticlesStore.hasLoaded" class="w-20 h-5" />
            <p v-if="myArticlesStore.preUploadDeadline">{{ myArticlesStore.preUploadDeadline ? formatDate(myArticlesStore.preUploadDeadline) : '' }}</p>
          </div>
        </Card>
        <Card class="p-5 flex flex-row gap-5 items-center">
          <CalendarDays class="h-14 w-14 text-destructive" />
          <div class="flex flex-col gap-3">
            <h2 class="font-semibold">Actual Deadline</h2>
            <Skeleton v-if="myArticlesStore.isLoading && !myArticlesStore.hasLoaded" class="w-20 h-5" />
            <p v-if="myArticlesStore.preUploadDeadline">{{ myArticlesStore.actualUploadDeadline ? formatDate(myArticlesStore.actualUploadDeadline) : '' }}</p>
          </div>
        </Card>
      </div>

      <div class="flex flex-col gap-3">
        <h3 class="font-semibold uppercase">Latest articles</h3>

        <LatestArticles />
      </div>

      <div class="flex flex-col gap-3">
        <div class="flex justify-between items-center">
          <h3 class="font-semibold uppercase">My Articles</h3>
          <Button variant="outline" size="sm" @click="myArticlesStore.refreshArticles()">
            Refresh
          </Button>
        </div>

        <div v-if="myArticlesStore.isLoading && !myArticlesStore.hasLoaded" class="space-y-4">
          <Skeleton v-for="i in 3" :key="i" class="h-36 w-full rounded-lg" />
        </div>

        <div v-else-if="myArticlesStore.error" class="p-4 bg-red-50 text-red-600 rounded-lg">
          {{ myArticlesStore.error }}
          <Button variant="outline" size="sm" class="ml-2" @click="myArticlesStore.fetchArticles(true)">
            Try Again
          </Button>
        </div>

        <div v-else-if="myArticlesStore.articles.length === 0" class="p-8 text-center bg-gray-50 rounded-lg">
          <p class="text-gray-500">No articles found.</p>
        </div>

        <div v-else class="space-y-4">
          <ArticlePost
            v-for="article in myArticlesStore.articles"
            :key="article.article_id"
            :article="{
              title: article.article_title,
              description: article.article_description,
              totalLikes: 0, // need data from BE
              totalViews: 0, 
              date: formatDate(article.created_at),
              status: myArticlesStore.statusText(article.status),
            }"
          />

          <Pagination
            v-if="myArticlesStore.totalPages > 1"
            :current-page="myArticlesStore.currentPage"
            :total-pages="myArticlesStore.totalPages"
            @page-change="myArticlesStore.setPage"
          />
        </div>
      </div>
    </div>
  </Layout>
</template>