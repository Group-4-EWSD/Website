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

// Date formatting
const preUploadDate = new Intl.DateTimeFormat('en', {
  day: 'numeric',
  month: 'short',
  year: 'numeric',
}).format(new Date())

const actualDeadlineDate = new Intl.DateTimeFormat('en', {
  day: 'numeric',
  month: 'short',
  year: 'numeric',
}).format(new Date())

const myArticlesStore = useMyArticlesStore()

onMounted(() => {
  // Only fetch if we haven't loaded data yet
  myArticlesStore.fetchArticles()
})

// Format date for display
const formatDate = (dateString: string): string => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
  })
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
            <p>{{ preUploadDate }}</p>
          </div>
        </Card>
        <Card class="p-5 flex flex-row gap-5 items-center">
          <CalendarDays class="h-14 w-14 text-destructive" />
          <div class="flex flex-col gap-3">
            <h2 class="font-semibold">Actual Deadline</h2>
            <p>{{ actualDeadlineDate }}</p>
          </div>
        </Card>
      </div>

      <div class="flex flex-col gap-3">
        <h3 class="font-semibold uppercase">Latest articles</h3>
        <LatestArticles />
      </div>

      <div class="flex flex-col gap-3">
        <h3 class="font-semibold uppercase">My Articles</h3>

        <div v-if="myArticlesStore.isLoading" class="space-y-4">
          <Skeleton v-for="i in 3" :key="i" class="h-36 w-full rounded-lg" />
        </div>

        <div v-else-if="myArticlesStore.error" class="p-4 bg-red-50 text-red-600 rounded-lg">
          {{ myArticlesStore.error }}
          <Button variant="outline" size="sm" class="ml-2" @click="myArticlesStore.fetchArticles(true)">
            Try Again
          </Button>
        </div>

        <div
          v-else-if="myArticlesStore.articles.length === 0"
          class="p-8 text-center bg-gray-50 rounded-lg"
        >
          <p class="text-gray-500">No articles found.</p>
        </div>

        <div v-else>
          <ArticlePost
            v-for="article in myArticlesStore.articles"
            :key="article.article_id"
            :article="{
              title: article.article_title,
              description: article.article_description, // No description in the API response
              totalLikes: myArticlesStore?.countData?.reactCount || 0,
              totalViews: myArticlesStore?.countData?.totalViewCount || 0,
              date: formatDate(article.created_at),
              status: myArticlesStore.statusText(article.status),
            }"
          />

          <Pagination
            :current-page="myArticlesStore.currentPage"
            :total-pages="myArticlesStore.totalPages"
            @page-change="myArticlesStore.setPage"
          />
        </div>
      </div>
    </div>
  </Layout>
</template>