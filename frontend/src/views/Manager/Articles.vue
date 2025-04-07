<script setup lang="ts">
import { ref, onMounted, watchEffect, computed } from 'vue'
import { Search } from 'lucide-vue-next'
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

// Store for persisting state across page visits
const articleStore = useArticleStore()
const selectedAcademicYearId = ref('')
const articleTitle = ref('')
const displayNumber = ref<number>(10)

// Fetch data only if not already loaded
onMounted(async () => {
  if (!articleStore.academicYearsLoaded) {
    await articleStore.fetchAcademicYears()
  }
  
  if (!articleStore.articlesLoaded) {
    await articleStore.fetchArticles({
      displayNumber: displayNumber.value,
      pageNumber: articleStore.currentPage,
      academicYearId: selectedAcademicYearId.value,
      articleTitle: articleTitle.value,
      status: 2
    })
  }
})

const goToPage = (page: number) => {
  articleStore.setCurrentPage(page)
  loadArticles()
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
    status: 2
  })
}

const handleSearch = () => {
  articleStore.setCurrentPage(1) // Reset to first page when searching
  loadArticles()
}

const handleAcademicYearChange = (value: string) => {
  selectedAcademicYearId.value = value
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
    case 0: return 'Pending'
    case 1: return 'In Review'
    case 2: return 'Approved'
    case 3: return 'Rejected'
    default: return 'Unknown'
  }
}

// Format date
const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
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
      <!-- Header with Title and Filters -->
      <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold">Articles Management</h2>
        
        <div class="flex flex-col sm:flex-row gap-4 w-full">
          <div class="relative w-[300px]">
            <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-gray-500" />
            <Input
              v-model="articleTitle"
              placeholder="Search articles..."
              class="pl-9"
            />
          </div>
          
          <Select :model-value="selectedAcademicYearId" @update:model-value="handleAcademicYearChange" class="w-full sm:min-w-[300px] sm:max-w-[300px]">
            <SelectTrigger>
              <SelectValue placeholder="All Academic Years" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="">All Academic Years</SelectItem>
              <SelectItem
                v-for="year in articleStore.academicYears"
                :key="year.academic_year_id"
                :value="year.academic_year_id"
              >
                {{ year.academic_year_description }}
              </SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>
      
      <!-- Articles Table -->
      <div class="rounded-md border">
        <Table>
          <TableHeader>
            <TableRow>
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
              <TableCell><Skeleton class="h-5 w-full max-w-xs" /></TableCell>
              <TableCell><Skeleton class="h-5 w-28" /></TableCell>
              <TableCell><Skeleton class="h-5 w-32" /></TableCell>
              <TableCell><Skeleton class="h-5 w-24" /></TableCell>
              <TableCell><Skeleton class="h-5 w-28" /></TableCell>
            </TableRow>
          </TableBody>
          
          <!-- Table Content -->
          <TableBody v-else-if="articleStore.articles.length > 0">
            <TableRow v-for="article in articleStore.articles" :key="article.article_id" class="hover:bg-gray-50">
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
      
      <!-- Pagination -->
      <div class="flex justify-between items-center p-4 mt-2">
        <div class="text-sm text-gray-500">
          Showing {{ ((articleStore.currentPage - 1) * displayNumber) + 1 }} to 
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
              :class="{ 'bg-primary text-primary-foreground hover:bg-primary hover:text-primary-foreground': page === articleStore.currentPage }"
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
            :disabled="articleStore.currentPage >= Math.ceil(articleStore.totalArticles / displayNumber)"
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
            :disabled="articleStore.currentPage >= Math.ceil(articleStore.totalArticles / displayNumber)"
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