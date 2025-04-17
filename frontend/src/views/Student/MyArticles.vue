<script setup lang="ts">
import { CalendarDays } from 'lucide-vue-next'
import { onMounted } from 'vue'

import ArticlePost from '@/components/pagespecific/my-articles/ArticlePost.vue'
import LatestArticles from '@/components/pagespecific/my-articles/LatestArticles.vue'
import UploadArticle from '@/components/pagespecific/my-articles/UploadArticle.vue'
import { Button } from '@/components/ui/button'
import { Card } from '@/components/ui/card'
import Layout from '@/components/ui/Layout.vue'
import {
  Pagination,
  PaginationEllipsis,
  PaginationFirst,
  PaginationLast,
  PaginationList,
  PaginationListItem,
  PaginationNext,
  PaginationPrev,
} from '@/components/ui/pagination'
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

const isOverPreUploadDeadline = (): boolean => {
  const preUploadDeadline = new Date(myArticlesStore.preUploadDeadline)
  const currentDate = new Date()
  return currentDate > preUploadDeadline
}

const isOverActualUploadDeadline = (): boolean => {
  const actualUploadDeadline = new Date(myArticlesStore.actualUploadDeadline)
  const currentDate = new Date()
  return currentDate > actualUploadDeadline
}
</script>

<template>
  <Layout>
    <h2 class="text-xl font-bold mb-4 uppercase">My Articles</h2>
    <div class="flex flex-col gap-5">
      <div class="grid md:grid-cols-3 grid-cols-1 gap-5">
        <Card class="p-5 flex flex-col gap-5">
          <h2 class="font-semibold uppercase">Upload Articles</h2>
          <p v-if="isOverActualUploadDeadline()" class="text-sm text-destructive">
            The deadline for uploading articles has passed. You can no longer edit your articles.
          </p>
          <p v-else-if="isOverPreUploadDeadline()" class="text-sm text-secondary">
            You cannot upload articles anymore. You can still edit your submitted articles.
          </p>
          <div v-else class="grid md:grid-cols-2 grid-cols-1 gap-3">
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

            <Skeleton
              v-if="myArticlesStore.isLoading && !myArticlesStore.hasLoaded"
              class="w-20 h-5"
            />
            <p v-if="myArticlesStore.preUploadDeadline">
              {{
                myArticlesStore.preUploadDeadline
                  ? formatDate(myArticlesStore.preUploadDeadline)
                  : ''
              }}
            </p>
          </div>
        </Card>
        <Card class="p-5 flex flex-row gap-5 items-center">
          <CalendarDays class="h-14 w-14 text-destructive" />
          <div class="flex flex-col gap-3">
            <h2 class="font-semibold">Actual Deadline</h2>
            <Skeleton
              v-if="myArticlesStore.isLoading && !myArticlesStore.hasLoaded"
              class="w-20 h-5"
            />
            <p v-if="myArticlesStore.preUploadDeadline">
              {{
                myArticlesStore.actualUploadDeadline
                  ? formatDate(myArticlesStore.actualUploadDeadline)
                  : ''
              }}
            </p>
          </div>
        </Card>
      </div>

      <div class="flex flex-col gap-3">
        <h3 class="font-semibold uppercase">Latest articles</h3>

        <div v-if="myArticlesStore.error" class="p-4 bg-red-50 text-red-600 rounded-lg">
          {{ myArticlesStore.error }}
          <Button
            variant="outline"
            size="sm"
            class="ml-2"
            @click="myArticlesStore.fetchArticles(true)"
          >
            Try Again
          </Button>
        </div>

        <LatestArticles v-else />
      </div>

      <div class="flex flex-col gap-3" v-if="!myArticlesStore.error">
        <div class="flex justify-between items-center">
          <h3 class="font-semibold uppercase">My Articles</h3>
        </div>

        <div v-if="myArticlesStore.isLoading && !myArticlesStore.hasLoaded" class="space-y-4">
          <Skeleton v-for="i in 3" :key="i" class="h-36 w-full rounded-lg" />
        </div>

        <div
          v-else-if="myArticlesStore.articles.length === 0"
          class="p-8 text-center bg-gray-50 rounded-lg"
        >
          <p class="text-gray-500">No articles found.</p>
        </div>

        <div v-else class="space-y-4">
          <ArticlePost
            v-for="article in myArticlesStore.articles"
            :key="article.article_id"
            :article="{
              id: article.article_id,
              title: article.article_title,
              description: article.article_description,
              totalLikes: article.react_count,
              totalViews: article.view_count,
              date: formatDate(article.created_at),
              status: article.status,
            }"
          />

          <!-- <Pagination
            v-if="myArticlesStore.totalPages > 1"
            :current-page="myArticlesStore.currentPage"
            :total-pages="myArticlesStore.totalPages"
            @page-change="myArticlesStore.setPage"
          /> -->

          <Pagination
            v-if="myArticlesStore.totalPages > 1"
            :items-per-page="10"
            :total="myArticlesStore.totalPages"
            :sibling-count="1"
            show-edges
            :default-page="myArticlesStore.currentPage"
            @update:page="myArticlesStore.setPage"
          >
            <PaginationList v-slot="{ items }" class="flex items-center gap-1">
              <PaginationFirst />
              <PaginationPrev />
              <template v-for="(item, index) in items" :key="index">
                <PaginationListItem v-if="item.type === 'page'" :value="item.value" as-child>
                  <Button
                    class="w-10 h-10 p-0"
                    :variant="item.value === myArticlesStore.currentPage ? 'default' : 'outline'"
                    @click="myArticlesStore.setPage(item.value)"
                  >
                    {{ item.value }}
                  </Button>
                </PaginationListItem>
                <PaginationEllipsis v-else />
              </template>
              <PaginationNext />
              <PaginationLast />
            </PaginationList>
          </Pagination>
        </div>
      </div>
    </div>
  </Layout>
</template>
