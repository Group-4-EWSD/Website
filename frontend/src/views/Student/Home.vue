<script setup lang="ts">
import { Eye, SlidersHorizontal, ThumbsUp } from 'lucide-vue-next'
import { onMounted, ref, watch } from 'vue'
import dayjs from 'dayjs'

import FilterModal from '@/components/pagespecific/student-home/FilterModal.vue'
import { Card } from '@/components/ui/card'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
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

import Layout from '@/components/ui/Layout.vue'
import { Table, TableBody, TableCell, TableRow } from '@/components/ui/table'
import { getArticles, getArticleDetails } from '@/api/articles'
import type { Articles, CountData } from '@/types/article'
import Button from '@/components/ui/button/Button.vue'
import { number } from 'yup'

const countData = ref<CountData | null>(null)
const articles = ref<Articles[]>([])
const sortOption = ref<string>('')
const isFetched = ref(false)
const currentPage = ref<number>(1)
const displayNumber = 5
const totalPages = ref(1)

const sortOptions = ref([
  { value: 'created asc', label: 'Newest First' },
  { value: 'created desc', label: 'Oldest First' },
  { value: 'title asc', label: 'Name (A → Z)' },
  { value: 'title-desc', label: 'Name (Z → A)' },
])

const sortBy = (option: string) => {
  sortOption.value = option
  console.log('Sorting by:', option)
}

const fetchArticles = async (page: number) => {
  try {
    const response = await getArticles({
      displayNumber,
      pageNumber: page,
      status: 0,
    })
    countData.value = response.countData
    articles.value = response.allArticles
    // totalPages.value = Math.ceil(articles.value.length / displayNumber)
    isFetched.value = true
  } catch (error) {
    console.error('Error fetching articles:', error)
  }
}

onMounted(() => {
  if (!isFetched.value) {
    console.log('ht', isFetched.value)
    fetchArticles(currentPage.value)
  }
})

watch(currentPage, fetchArticles)

const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    fetchArticles(page)
  }
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
              {{ countData?.reactCount }} <span class="text-primary text-3xl"> Likes</span>
            </p>
            <p class="text-sm text-muted-foreground">Up to 10% from Last Week</p>
          </div>
        </Card>
        <Card class="p-4">
          <div class="flex items-center justify-between">
            <h2 class="text-lg uppercase">Uploaded Articles</h2>
          </div>
          <div>
            <p class="text-4xl font-bold py-2">{{ countData?.currentYearArticleCount }}</p>
            <p class="text-sm text-muted-foreground">Articles for this year</p>
          </div>
        </Card>
        <Card class="p-4">
          <div class="flex items-center justify-between">
            <h2 class="text-lg uppercase">Total Views</h2>
            <component :is="Eye" />
          </div>
          <div>
            <p class="text-4xl font-bold py-2">{{ countData?.totalViewCount }}</p>
            <p class="text-sm text-muted-foreground">75% total numbers of views</p>
          </div>
        </Card>
      </div>

      <div class="flex justify-between items-center pb-2 relative">
        <h3 class="font-semibold uppercase">AURORA's magazine articles</h3>
        <div class="flex gap-3 text-gray-600 pr-[10px] relative">
          <!-- Filter -->
          <FilterModal />

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
        <!-- <div class="flex flex-col gap-3"> -->
        <div class="max-h-[400px] overflow-y-auto">
          <Table class="w-full">
            <TableBody>
              <TableRow
                v-for="article in articles.slice(0, 6)"
                :key="article.article_id"
                class="border-b hover:bg-gray-50 transition-all"
              >
                <TableCell>
                  <div class="flex items-center gap-4">
                    <img
                      src="@/assets/profile.png"
                      class="w-8 h-8 sm:w-10 sm:h-10 rounded-full border border-white"
                    />
                    <div class="flex-1">
                      <router-link
                        :to="{ name: 'getArticleDetails', params: { id: article.article_id } }"
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
            </TableBody>
          </Table>

          <Pagination
            v-slot=""
            :items-per-page="displayNumber"
            :total="24"
            :sibling-count="1"
            show-edges
            :default-page="currentPage"
            @update:page="goToPage"
          >
            <PaginationList v-slot="{ items }" class="flex items-center gap-1">
              <PaginationFirst />
              <PaginationPrev />
              <template v-for="(item, index) in items" :key="index">
                <PaginationListItem v-if="item.type === 'page'" :value="item.value" as-child>
                  <Button
                    class="w-10 h-10 p-0"
                    :variant="item.value === currentPage ? 'default' : 'outline'"
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
  </Layout>
</template>
