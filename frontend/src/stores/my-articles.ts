import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import { getMyArticles } from '@/api/articles'
import type { ArticleParams, Articles, CountData } from '@/types/article'

export const useMyArticlesStore = defineStore('myArticles', () => {
  // State
  const articles = ref<Articles[]>([])
  const countData = ref<CountData | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const currentPage = ref(1)
  const pageSize = ref(10)
  const totalCount = ref(0)
  const hasLoaded = ref(false) // New flag to track if data has been loaded
  const filters = ref<Omit<ArticleParams, 'pageNumber' | 'displayNumber'>>({
    academicYearId: undefined,
    articleTitle: undefined,
    sorting: undefined,
    status: undefined
  })

  // Getters
  const totalPages = computed(() => Math.ceil(totalCount.value / pageSize.value))
  const statusText = computed(() => {
    return (status: number): string => {
      switch (status) {
        case 0: return 'Draft'
        case 1: return 'Pending'
        case 2: return 'Approved'
        case 3: return 'Rejected'
        default: return 'Unknown'
      }
    }
  })

  // Actions
  const fetchArticles = async (forceRefresh = false) => {
    // Skip fetching if data is already loaded and no force refresh
    if (hasLoaded.value && !forceRefresh && !isLoading.value) {
      return
    }

    isLoading.value = true
    error.value = null
    
    try {
      const response = await getMyArticles({
        pageNumber: currentPage.value,
        displayNumber: pageSize.value,
        ...filters.value
      })
      
      articles.value = response.allArticles || []
      countData.value = response.countData || null
      
      // Fix the total count calculation
      totalCount.value = response.countData?.currentYearArticleCount || articles.value.length
      
      // Mark as loaded
      hasLoaded.value = true
    } catch (err) {
      console.error('Error fetching articles:', err)
      error.value = 'Failed to load articles. Please try again.'
    } finally {
      isLoading.value = false
    }
  }

  const setPage = (page: number) => {
    currentPage.value = page
    fetchArticles(true) // Force refresh when changing page
  }

  const setPageSize = (size: number) => {
    pageSize.value = size
    currentPage.value = 1 // Reset to first page when changing page size
    fetchArticles(true) // Force refresh when changing page size
  }

  const setFilters = (newFilters: Partial<typeof filters.value>) => {
    filters.value = { ...filters.value, ...newFilters }
    currentPage.value = 1 // Reset to first page when changing filters
    fetchArticles(true) // Force refresh when changing filters
  }

  const resetFilters = () => {
    filters.value = {
      academicYearId: undefined,
      articleTitle: undefined,
      sorting: undefined,
      status: undefined
    }
    currentPage.value = 1
    fetchArticles(true) // Force refresh when resetting filters
  }

  const refreshArticles = () => {
    fetchArticles(true) // Explicit method to force a refresh
  }

  const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit'
    })
  }

  return {
    // State
    articles,
    countData,
    isLoading,
    error,
    currentPage,
    pageSize,
    totalCount,
    filters,
    hasLoaded,
    // Getters
    totalPages,
    statusText,
    // Actions
    fetchArticles,
    refreshArticles, // New explicit refresh method
    setPage,
    setPageSize,
    setFilters,
    resetFilters,
    formatDate
  }
})