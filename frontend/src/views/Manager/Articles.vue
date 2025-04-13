<script setup lang="ts">
import { ref, onMounted, watchEffect, computed } from 'vue'
import { Search, Download, CheckSquare, Square } from 'lucide-vue-next'
import Layout from '@/components/ui/Layout.vue'
import Skeleton from '@/components/ui/skeleton/Skeleton.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { useArticleStore } from '@/stores/manager-articles'
import { Checkbox } from '@/components/ui/checkbox'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { toast } from 'vue-sonner'
import { downloadArticles } from '@/api/articles'
import type { AcceptableValue } from 'reka-ui'

// Store for persisting state across page visits
const articleStore = useArticleStore()
const selectedAcademicYearId = ref('')
const articleTitle = ref('')
const displayNumber = ref<number>(10)
const downloadingArticles = ref(false)

// Selection functionality
const selectedArticles = ref<string[]>([])
const allSelected = computed(() => {
  return (
    articleStore.articles.length > 0 &&
    selectedArticles.value.length === articleStore.articles.length
  )
})

// Fetch data only if not already loaded
onMounted(async () => {
  if (!articleStore.academicYearsLoaded) {
    await articleStore.fetchAcademicYears()
    selectedAcademicYearId.value = articleStore.academicYears.length
      ? articleStore.academicYears[0].academic_year_id
      : ''
  }

  if (!articleStore.articlesLoaded) {
    await articleStore.fetchArticles({
      displayNumber: displayNumber.value,
      pageNumber: articleStore.currentPage,
      academicYearId: selectedAcademicYearId.value,
      articleTitle: articleTitle.value,
      status: 2,
    })
  }
})

// Selection functions
const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedArticles.value = []
  } else {
    selectedArticles.value = articleStore.articles.map((article) => article.article_id)
  }
}

const toggleArticleSelection = (articleId: string) => {
  const index = selectedArticles.value.indexOf(articleId)
  if (index === -1) {
    selectedArticles.value.push(articleId)
  } else {
    selectedArticles.value.splice(index, 1)
  }
}

const isArticleSelected = (articleId: string) => {
  return selectedArticles.value.includes(articleId)
}

// Download selected articles
const downloadSelectedArticles = () => {
  if (selectedArticles.value.length === 0) {
    toast.error('Please select at least one article to download.')
    return
  }
  downloadingArticles.value = true
  downloadArticles(selectedArticles.value)
    .then((res) => {
      // download from link
      const url = res.data.url
      const link = document.createElement('a')
      link.href = url
      link.download = res.data.filename || 'articles.zip'
      link.target = '_blank'
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link) 

      toast.success('Download started. Check your downloads folder.')
      selectedArticles.value = []
    })
    .catch((error) => {
      toast.error('Error downloading articles: ' + error.message)
    })
    .finally(() => {
      downloadingArticles.value = false
    })
}

const goToPage = (page: number) => {
  articleStore.setCurrentPage(page)
  loadArticles()
  // Clear selection when changing pages
  selectedArticles.value = []
}

const goToFirstPage = () => {
  goToPage(1)
}

const goToPrevPage = () => {
  if (articleStore.currentPage > 1) {
    goToPage(articleStore.currentPage - 1)
  }
}

const goToNextPage = () => {
  if (articleStore.currentPage < Math.ceil(articleStore.totalArticles / displayNumber.value)) {
    goToPage(articleStore.currentPage + 1)
  }
}

const goToLastPage = () => {
  const lastPage = Math.ceil(articleStore.totalArticles / displayNumber.value)
  goToPage(lastPage)
}

const loadArticles = () => {
  articleStore.fetchArticles({
    displayNumber: displayNumber.value,
    pageNumber: articleStore.currentPage,
    academicYearId: selectedAcademicYearId.value,
    articleTitle: articleTitle.value,
    status: 2,
  })
}

const handleSearch = () => {
  articleStore.setCurrentPage(1) // Reset to first page when searching
  loadArticles()
}

const handleAcademicYearChange = (value: AcceptableValue) => {
  selectedAcademicYearId.value = value ? value.toString() : ''
  articleStore.setCurrentPage(1) // Reset to first page when changing filter
  loadArticles()
}

const handleDisplayNumberChange = (value: string) => {
  displayNumber.value = parseInt(value)
  articleStore.setCurrentPage(1) // Reset to first page when changing items per page
  loadArticles()
}

// Get status display name
const getStatusDisplay = (status: number) => {
  switch (status) {
    case 0:
      return 'Draft'
    case 1:
      return 'Submitted'
    case 2:
      return 'Approved'
    case 3:
      return 'Rejected'
    case 4:
      return 'Published'
    default:
      return 'Unknown'
  }
}

// Get status badge color
const getStatusBadgeVariant = (status: number) => {
  switch (status) {
    case 0:
      return 'secondary'
    case 1:
      return 'warning'
    case 2:
      return 'success'
    case 3:
      return 'destructive'
    case 4:
      return 'primary'
    default:
      return 'outline'
  }
}

// Format date
const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

// Generate page numbers for pagination
const pageNumbers = computed(() => {
  const totalPages = Math.ceil(articleStore.totalArticles / displayNumber.value)
  const currentPage = articleStore.currentPage

  let pages = []

  // Always include page 1
  pages.push(1)

  // Add ellipsis if needed
  if (currentPage > 3) {
    pages.push('ellipsis')
  }

  // Add pages around current page
  for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
    if (i !== 1 && i !== totalPages) {
      pages.push(i)
    }
  }

  // Add ellipsis if needed
  if (currentPage < totalPages - 2) {
    pages.push('ellipsis')
  }

  // Add last page if there is more than one page
  if (totalPages > 1) {
    pages.push(totalPages)
  }

  return pages
})

// Watch for changes in articleTitle with debounce
let debounceTimeout: number | null = null
watchEffect(() => {
  const searchTerm = articleTitle.value

  if (debounceTimeout) {
    clearTimeout(debounceTimeout)
  }

  debounceTimeout = setTimeout(() => {
    if (searchTerm === articleTitle.value) {
      handleSearch()
    }
  }, 500)
})
</script>

<template>
  <Layout>
    <div class="flex flex-col">
      <!-- Header with Title and Actions -->
      <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-4 gap-4">
        <h2 class="text-2xl font-bold">Articles</h2>

        <div class="flex items-center gap-3 w-full md:w-auto flex-col md:flex-row">
          <div class="relative w-full md:min-w-[300px] md:max-w-md items-center">
            <Input v-model="articleTitle" placeholder="Search articles..." class="pl-9" />
            <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
              <Search class="size-6 text-muted-foreground" />
            </span>
          </div>

          <Select v-model="selectedAcademicYearId" @update:modelValue="handleAcademicYearChange">
            <SelectTrigger class="w-full md:min-w-[300px] md:max-w-md">
              <SelectValue placeholder="All Academic Years" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem
                v-for="year in articleStore.academicYears"
                :key="year.academic_year_id"
                :value="year.academic_year_id"
              >
                {{ year.academic_year_description }}
              </SelectItem>
            </SelectContent>
          </Select>

          <Button
            class="items-center gap-2 hidden md:flex"
            :disabled="selectedArticles.length === 0"
            @click="downloadSelectedArticles"
          >
            <Download class="h-4 w-4" />
            Download
          </Button>
        </div>
      </div>

      <!-- Desktop View: Articles Table -->
      <div class="rounded-md border hidden md:block">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead class="w-14">
                <div class="flex items-center justify-center">
                  <Checkbox
                    :model-value="allSelected"
                    @update:modelValue="toggleSelectAll"
                    id="select-all"
                  />
                </div>
              </TableHead>
              <TableHead>Title</TableHead>
              <TableHead>Submission Date</TableHead>
              <TableHead>Submitted By</TableHead>
              <TableHead>Status</TableHead>
              <TableHead>Category</TableHead>
            </TableRow>
          </TableHeader>

          <!-- Loading state with skeletons -->
          <TableBody v-if="articleStore.isLoading">
            <TableRow v-for="i in displayNumber" :key="i">
              <TableCell
                ><div class="flex justify-center"><Skeleton class="h-5 w-5" /></div
              ></TableCell>
              <TableCell><Skeleton class="h-5 w-28 max-w-xs" /></TableCell>
              <TableCell><Skeleton class="h-5 w-28" /></TableCell>
              <TableCell><Skeleton class="h-5 w-32" /></TableCell>
              <TableCell><Skeleton class="h-5 w-24" /></TableCell>
              <TableCell><Skeleton class="h-5 w-28" /></TableCell>
            </TableRow>
          </TableBody>

          <!-- Table Content -->
          <TableBody v-else-if="articleStore.articles.length > 0">
            <TableRow
              v-for="article in articleStore.articles"
              :key="article.article_id"
              class="hover:bg-gray-50"
            >
              <TableCell>
                <div class="flex items-center justify-center">
                  <Checkbox
                    :model-value="isArticleSelected(article.article_id)"
                    @update:modelValue="toggleArticleSelection(article.article_id)"
                    :id="`article-${article.article_id}`"
                  />
                </div>
              </TableCell>
              <TableCell class="font-medium">{{ article.article_title }}</TableCell>
              <TableCell>{{ formatDate(article.created_at) }}</TableCell>
              <TableCell>{{ article.user_name }}</TableCell>
              <TableCell>{{ getStatusDisplay(article.status) }}</TableCell>
              <TableCell>{{ article.article_type_name }}</TableCell>
            </TableRow>
          </TableBody>

          <!-- Empty state -->
          <TableCaption v-if="!articleStore.isLoading && articleStore.articles.length === 0">
            No articles found matching your criteria.
          </TableCaption>
        </Table>
      </div>

      <!-- Mobile View: Cards -->
      <div class="md:hidden space-y-4">
        <!-- Select All for Mobile -->
        <div
          class="flex items-center justify-between p-2 border rounded-md bg-gray-50"
          v-if="!articleStore.isLoading && articleStore.articles.length > 0"
        >
          <div class="flex items-center gap-2">
            <Checkbox
              :model-value="allSelected"
              @update:model-value="toggleSelectAll"
              id="mobile-select-all"
            />
            <label for="mobile-select-all" class="text-sm font-medium">
              Select All ({{ selectedArticles.length }}/{{ articleStore.articles.length }})
            </label>
          </div>
          <Button
            size="sm"
            class="flex items-center gap-1"
            :disabled="selectedArticles.length === 0"
            @click="downloadSelectedArticles"
          >
            <Download class="h-3 w-3" />
            Download
          </Button>
        </div>

        <!-- Loading skeletons for mobile -->
        <div v-if="articleStore.isLoading">
          <div v-for="i in displayNumber" :key="i" class="border rounded-lg mb-4">
            <div class="p-4 space-y-3">
              <Skeleton class="h-6 w-3/4" />
              <div class="flex justify-between">
                <Skeleton class="h-5 w-24" />
                <Skeleton class="h-5 w-20" />
              </div>
              <div class="flex justify-between">
                <Skeleton class="h-5 w-32" />
                <Skeleton class="h-5 w-16" />
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile Cards -->
        <div v-else-if="articleStore.articles.length > 0">
          <Card v-for="article in articleStore.articles" :key="article.article_id" class="mb-4">
            <CardHeader class="p-4">
              <div class="flex items-start justify-between">
                <div class="flex items-center gap-2">
                  <Checkbox
                    :model-value="isArticleSelected(article.article_id)"
                    @update:modelValue="toggleArticleSelection(article.article_id)"
                    :id="`mobile-article-${article.article_id}`"
                  />
                  <CardTitle class="text-base line-clamp-2 overflow-hidden text-ellipsis">
                    {{ article.article_title }}
                  </CardTitle>
                </div>
                <Badge>
                  {{ getStatusDisplay(article.status) }}
                </Badge>
              </div>
            </CardHeader>
            <CardContent class="p-4 py-2">
              <div class="text-sm space-y-1">
                <div class="flex justify-between">
                  <span class="text-gray-500">Submitted by:</span>
                  <span class="font-medium">{{ article.user_name }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Date:</span>
                  <span>{{ formatDate(article.created_at) }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Category:</span>
                  <span>{{ article.article_type_name }}</span>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Empty state for mobile -->
        <div
          v-if="!articleStore.isLoading && articleStore.articles.length === 0"
          class="text-center py-8 border rounded-md"
        >
          No articles found matching your criteria.
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex flex-col sm:flex-row justify-between items-center p-4 mt-2 gap-4">
        <div class="text-sm text-gray-500">
          Showing {{ (articleStore.currentPage - 1) * displayNumber + 1 }} to
          {{ Math.min(articleStore.currentPage * displayNumber, articleStore.totalArticles) }}
          of {{ articleStore.totalArticles }} articles
        </div>

        <div class="flex items-center gap-1">
          <!-- First Page -->
          <Button
            variant="outline"
            size="icon"
            class="h-10 w-10"
            :disabled="articleStore.currentPage === 1"
            @click="goToFirstPage"
            title="First Page"
          >
            «
          </Button>

          <!-- Previous Page -->
          <Button
            variant="outline"
            size="icon"
            class="h-10 w-10"
            :disabled="articleStore.currentPage === 1"
            @click="goToPrevPage"
            title="Previous Page"
          >
            ‹
          </Button>

          <!-- Page Numbers -->
          <template v-for="(page, index) in pageNumbers" :key="index">
            <div v-if="page === 'ellipsis'" class="mx-1">...</div>
            <Button
              v-else
              variant="outline"
              class="h-10 w-10"
              :class="{
                'bg-primary text-primary-foreground hover:bg-primary hover:text-primary-foreground':
                  page === articleStore.currentPage,
              }"
              @click="goToPage(Number(page))"
            >
              {{ page }}
            </Button>
          </template>

          <!-- Next Page -->
          <Button
            variant="outline"
            size="icon"
            class="h-10 w-10"
            :disabled="
              articleStore.currentPage >= Math.ceil(articleStore.totalArticles / displayNumber)
            "
            @click="goToNextPage"
            title="Next Page"
          >
            ›
          </Button>

          <!-- Last Page -->
          <Button
            variant="outline"
            size="icon"
            class="h-10 w-10"
            :disabled="
              articleStore.currentPage >= Math.ceil(articleStore.totalArticles / displayNumber)
            "
            @click="goToLastPage"
            title="Last Page"
          >
            »
          </Button>
        </div>
      </div>
    </div>
  </Layout>
</template>
