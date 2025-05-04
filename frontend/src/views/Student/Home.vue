<script setup lang="ts">
import dayjs from 'dayjs'
import { Eye, SlidersHorizontal, ThumbsUp } from 'lucide-vue-next'
import { computed, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'

import { getFilterItems } from '@/api/articles'
import FilterModal from '@/components/pagespecific/student-home/FilterModal.vue'
import TooltipWrapper from '@/components/shared/TooltipWrapper.vue'
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
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { getInitials } from '@/lib/utils'

const router = useRouter()
const articleStore = useArticleStore()

const categoryOptions = ref<{ label: string; value: string }[]>([])
const yearOptions = ref<{ label: string; value: string }[]>([])
const sortedValue = ref('created asc')

const selectedCategory = computed({
  get: () => articleStore.selectedCategory,
  set: (val) => (articleStore.selectedCategory = val),
})

const selectedYear = computed({
  get: () => articleStore.selectedYear,
  set: (val) => (articleStore.selectedYear = val),
})

const sortOptions = ref([
  { value: '1', label: 'Newest First' },
  { value: '2', label: 'Oldest First' },
  { value: '3', label: 'Name (A → Z)' },
  { value: '4', label: 'Name (Z → A)' },
])

const goToArticleDetails = (articleId: string) => {
  router.push({ name: 'getArticleDetails', params: { id: articleId } })
}

const sortBy = (option: string) => {
  sortedValue.value = option
  articleStore.sortOption = option
  articleStore.fetchArticles({ pageNumber: 1 })
}

onMounted(async () => {
  if (!articleStore.articles.length) {
    articleStore.fetchArticles({ pageNumber: 1 })
  }
  if (!articleStore.categoryOptions.length || !articleStore.yearOptions.length) {
    const [articleTypes, academicYears] = await Promise.all([getFilterItems(3), getFilterItems(4)])

    categoryOptions.value = articleTypes.map((item: any) => ({
      label: item.faculty_name,
      value: item.faculty_id,
    }))

    yearOptions.value = academicYears.map((item: any) => ({
      label: item.academic_year_description,
      value: item.academic_year_id,
    }))

    articleStore.setCategoryOptions(categoryOptions.value)
    articleStore.setYearOptions(yearOptions.value)
  }
})

watch(
  () => articleStore.currentPage,
  (newPage) => {
    articleStore.fetchArticles({ pageNumber: newPage })
  },
)

watch(
  [
    () => articleStore.sortOption,
    () => articleStore.selectedCategory,
    () => articleStore.selectedYear,
  ],
  () => {
    articleStore.currentPage = 1
    articleStore.fetchArticles({ pageNumber: 1 })
    articleStore.hasFiltered = true
  },
)

const updateCategory = (newCategory: string) => {
  selectedCategory.value = newCategory
  articleStore.selectedCategory = newCategory
  articleStore.fetchArticles({ pageNumber: 1 })
}

const updateYear = (newYear: string) => {
  selectedYear.value = newYear
  articleStore.selectedYear = newYear
  articleStore.fetchArticles({ pageNumber: 1 })
}

const goToPage = (page: number) => {
  articleStore.currentPage = page
}
</script>

<template>
  <Layout>
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-xl font-bold uppercase">Dashboard</h2>

      <div class="text-sm text-gray-500">
        <template v-if="articleStore.isLoading">
          <span class="animate-pulse text-gray-400">Loading...</span>
        </template>
        <template v-else> Previous Login {{ articleStore.prevLogin || '-' }} </template>
      </div>
    </div>

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
            <p class="text-sm text-muted-foreground">All-time article uploads</p>
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
            :category-options="articleStore.categoryOptions"
            :year-options="articleStore.yearOptions"
            :selected-category="articleStore.selectedCategory"
            :selected-year="articleStore.selectedYear"
            @update:selectedCategory="updateCategory"
            @update:selectedYear="updateYear"
          />

          <!-- Sorting -->
          <DropdownMenu>
            <TooltipWrapper text="Sort">
              <DropdownMenuTrigger as-child>
                <SlidersHorizontal
                  class="w-5 h-5 cursor-pointer hover:text-black transition-all"
                  aria-label="Sort items"
                />
              </DropdownMenuTrigger>
            </TooltipWrapper>

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
        <div class="max-h-[480px] overflow-y-auto">
          <div v-if="articleStore.error" class="p-4 bg-red-50 text-red-600 rounded-lg">
            {{ articleStore.error }}
            <Button
              variant="outline"
              size="sm"
              class="ml-2"
              @click="articleStore.fetchArticles({ pageNumber: 1 })"
            >
              Try Again
            </Button>
          </div>

          <Table v-else class="w-full">
            <TableBody>
              <TableRow
                v-if="
                  !articleStore.isLoading &&
                  articleStore.articles.length === 0 &&
                  articleStore.hasFiltered
                "
              >
                <TableCell colspan="100%" class="text-center py-6 text-gray-500">
                  No articles found with the current filters.
                </TableCell>
              </TableRow>

              <TableRow
                v-if="articleStore.isLoading"
                v-for="n in 5"
                :key="n"
                class="border-b hover:bg-gray-50 transition-all"
              >
                <TableCell>
                  <Skeleton class="h-12" animate-pulse />
                </TableCell>
              </TableRow>

              <TableRow
                v-else
                v-for="article in articleStore.articles.slice(0, 5)"
                :key="article.article_id"
                class="border-b hover:bg-gray-50 transition-all cursor-pointer"
                @click="goToArticleDetails(article.article_id || '')"
              >
                <TableCell>
                  <div class="flex items-center gap-4">
                    <Avatar>
                      <AvatarImage :src="article.user_photo_path" />
                      <AvatarFallback class="text-white">{{
                        getInitials(article.user_name || 'U')
                      }}</AvatarFallback>
                    </Avatar>
                    <div class="flex-1">
                      <router-link
                        :to="`/articles/${article.article_id}`"
                        class="text-primary font-semibold py-1"
                      >
                        {{ article.article_title }}
                      </router-link>
                      <p class="text-sm text-gray-500 py-1">
                        {{ article.article_description }}
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
          <div class="flex justify-end mt-4">
            <Pagination
              v-slot=""
              :items-per-page="articleStore.displayNumber"
              :total="articleStore.articleCount"
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
