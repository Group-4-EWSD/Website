<script setup lang="ts">
import dayjs from 'dayjs'
import { ArrowUpToLine, Eye, SlidersHorizontal, ThumbsUp } from 'lucide-vue-next'
import { onMounted, ref, watch } from 'vue'
import { computed } from 'vue'
import { useRouter } from 'vue-router'

import { getArticles } from '@/api/articles'
import FilterModal from '@/components/pagespecific/student-home/FilterModal.vue'
import Button from '@/components/ui/button/Button.vue'
import { Card } from '@/components/ui/card'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
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
import { Table, TableBody, TableCell, TableRow } from '@/components/ui/table'
import { useArticleStore } from '@/stores/articles'
import type { Article } from '@/types/article'


const router = useRouter()
const articleStore = useArticleStore()
const { fetchArticles, displayNumber } = articleStore

const articles = ref<Article[]>([])
const selectedCategory = ref('all')
const selectedYear = ref('all')
const sortedValue = ref('created asc')

const sortOptions = ref([
  { value: 'created asc', label: 'Newest First' },
  { value: 'created desc', label: 'Oldest First' },
  { value: 'title asc', label: 'Name (A → Z)' },
  { value: 'title-desc', label: 'Name (Z → A)' },
])

const goToArticleDetails = (articleId: string) => {
  router.push({ name: 'getArticleDetails', params: { id: articleId } })
}

const sortBy = (option: string) => {
  sortedValue.value = option
  articleStore.sortOption = option
  console.log('Sorting by:', option)
}

onMounted(() => {
  if (!articleStore.articles.length) {
    articleStore.fetchArticles(articleStore.currentPage)
  }
})

watch([() => articleStore.currentPage, () => sortedValue.value], ([newPage, newSort]) => {
  articleStore.fetchArticles(newPage)
})

const updateCategory = (newCategory: string) => {
  selectedCategory.value = newCategory
}

const updateYear = (newYear: string) => {
  selectedYear.value = newYear
}

const goToPage = (page: number) => {
  articleStore.currentPage = page
}
</script>

<template>
  <Layout>
    <h2 class="text-xl font-bold mb-4 uppercase">Dashboard</h2>

    <div class="flex flex-col gap-3">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <Card class="p-4">
          <div class="flex items-center justify-between p-0">
            <h2 class="text-lg uppercase">Total Likes</h2>
            <component :is="ThumbsUp" />
          </div>
          <div>
            <p class="text-4xl font-bold text-blue-500 py-2">
              {{ articleStore.countData?.reactCount || '0' }}
              <span class="text-primary text-3xl"> Likes</span>
            </p>
            <p class="text-sm text-muted-foreground">Up to 10% from Last Week</p>
          </div>
        </Card>
        <Card class="p-4">
          <div class="flex items-center justify-between">
            <h2 class="text-lg uppercase">Uploaded Articles</h2>
          </div>
          <div>
            <p class="text-4xl font-bold py-2">
              {{ articleStore.countData?.currentYearArticleCount || '0' }}
            </p>
            <p class="text-sm text-muted-foreground">Articles for this year</p>
          </div>
        </Card>
        <Card class="p-4">
          <div class="flex items-center justify-between">
            <h2 class="text-lg uppercase">Total Views</h2>
            <component :is="Eye" />
          </div>
          <div>
            <p class="text-4xl font-bold py-2">
              {{ articleStore.countData?.totalViewCount || '0' }}
            </p>
            <p class="text-sm text-muted-foreground">75% total numbers of views</p>
          </div>
        </Card>
      </div>

      <div class="flex justify-between items-center pb-2 relative">
        <h3 class="font-semibold uppercase">AURORA's magazine articles</h3>
        <div class="flex gap-3 text-gray-600 pr-[10px] relative">
          <!-- Filter -->
          <FilterModal
            :selected-category="selectedCategory"
            :selected-year="selectedYear"
            @update:selectedCategory="updateCategory"
            @update:selectedYear="updateYear"
          />

          <!-- Sorting -->
          <DropdownMenu>
            <DropdownMenuTrigger as-child>
              <SlidersHorizontal
                class="w-5 h-5 cursor-pointer hover:text-black transition-all"
                aria-label="Sort items"
              />
            </DropdownMenuTrigger>

            <DropdownMenuContent align="end" class="w-48 z-50">
              <DropdownMenuLabel>Sort By</DropdownMenuLabel>
              <DropdownMenuSeparator />
              <DropdownMenuItem
                v-for="option in sortOptions"
                :key="option.value"
                @click="sortBy(option.value)"
              >
                {{ option.label }}
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </div>
      <div class="w-full border rounded-lg shadow-sm bg-white p-4 relative">
        <div class="max-h-[400px] overflow-y-auto">
          <div v-if="!articleStore.articles">
            <p>No articles found.</p>
          </div>
          <Table v-else class="w-full">
            <TableBody>
              <TableRow
                v-if="!articleStore.isLoading"
                v-for="article in articleStore.articles.slice(0, 6)"
                :key="article.article_id"
                class="border-b hover:bg-gray-50 transition-all cursor-pointer"
                @click="goToArticleDetails(article.article_id || '')"
              >
                <TableCell>
                  <div class="flex items-center gap-4">
                    <img
                      :src="article.user_photo_path"
                      class="w-8 h-8 sm:w-10 sm:h-10 rounded-full border border-white"
                    />
                    <div class="flex-1">
                      <router-link
                        :to="`/articles/${article.article_id}`"
                        class="text-blue-600 font-semibold hover:underline py-1"
                      >
                        {{ article.article_title }}
                      </router-link>
                      <p class="text-sm text-gray-500 py-1">
                        {{ article.article_title }}
                      </p>
                    </div>
                    <span class="text-gray-600 text-sm">
                      {{ dayjs(article.created_at).format('MMM D, YYYY') }}
                    </span>
                  </div>
                </TableCell>
              </TableRow>
              <TableRow
                v-else
                v-for="n in 5"
                :key="n"
                class="border-b hover:bg-gray-50 transition-all"
              >
                <TableCell>
                  <Skeleton class="h-12" animate-pulse />
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
          <div class="flex justify-end mt-4">
            <Pagination
              v-slot=""
              :items-per-page="displayNumber"
              :total="24"
              :sibling-count="1"
              show-edges
              :default-page="articleStore.currentPage"
              @update:page="goToPage"
            >
              <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                <PaginationFirst />
                <PaginationPrev />
                <template v-for="(item, index) in items" :key="index">
                  <PaginationListItem v-if="item.type === 'page'" :value="item.value" as-child>
                    <Button
                      class="w-10 h-10 p-0"
                      :variant="item.value === articleStore.currentPage ? 'default' : 'outline'"
                      @click="goToPage(item.value)"
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
    </div>
  </Layout>
</template>
