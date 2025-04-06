// src/stores/article.ts
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api/axios'
import { getAcademicYearList } from '@/api/academic-years'

export interface AcademicYear {
  academic_year_id: string
  academic_year_description: string
}

export interface Article {
  article_id: string
  article_title: string
  article_description: string
  article_type_id: string
  article_type_name: string
  user_id: string
  user_name: string
  user_photo_path: string
  gender: number
  created_at: string
  updated_at: string
  file_path?: string | null
  status: number
  view_count: number
  react_count: number
  comment_count: number
  current_user_react: number
  last_feedback: string
}

interface FetchArticlesParams {
  displayNumber: number
  pageNumber: number
  academicYearId?: string
  articleTitle?: string
  sorting?: string
  status?: number
}

export const useArticleStore = defineStore('article', () => {
  // State
  const articles = ref<Article[]>([])
  const academicYears = ref<AcademicYear[]>([])
  const currentPage = ref(1)
  const totalArticles = ref(0)
  const isLoading = ref(false)
  
  // Cache state flags
  const articlesLoaded = ref(false)
  const academicYearsLoaded = ref(false)
  const lastFetchParams = ref<FetchArticlesParams | null>(null)
  
  // Cache keyed by parameters
  const articlesCache = ref<Record<string, { data: Article[], total: number }>>({})

  // Getters
  const getArticleById = computed(() => {
    return (id: string) => articles.value.find(article => article.article_id === id)
  })

  // Generate cache key from params
  const getCacheKey = (params: FetchArticlesParams): string => {
    return JSON.stringify({
      displayNumber: params.displayNumber,
      pageNumber: params.pageNumber,
      academicYearId: params.academicYearId || '',
      articleTitle: params.articleTitle || '',
      status: params.status || 2
    })
  }

  // Actions
  const fetchArticles = async (params: FetchArticlesParams) => {
    const cacheKey = getCacheKey(params)
    
    // Check if we have cached data for these params
    if (articlesCache.value[cacheKey]) {
      articles.value = articlesCache.value[cacheKey].data
      totalArticles.value = articlesCache.value[cacheKey].total
      lastFetchParams.value = { ...params }
      articlesLoaded.value = true
      return
    }
    
    try {
      isLoading.value = true
      const response = await api.get('/articles/manager', {
        params: {
          displayNumber: params.displayNumber,
          pageNumber: params.pageNumber,
          academicYearId: params.academicYearId || '',
          articleTitle: params.articleTitle || '',
          sorting: params.sorting || '',
          status: params.status !== undefined ? params.status : 2 // Default to status 2
        }
      })

      const fetchedArticles = response.data
      const total = 100
      // const total = response.data.totalCount

      // Update the cache
      articlesCache.value[cacheKey] = {
        data: fetchedArticles,
        total: total
      }
      
      // Update the state
      articles.value = fetchedArticles
      totalArticles.value = total
      lastFetchParams.value = { ...params }
      articlesLoaded.value = true
    } catch (error) {
      console.error('Error fetching articles:', error)
    } finally {
      isLoading.value = false
    }
  }

  const fetchAcademicYears = async () => {
    if (academicYearsLoaded.value && academicYears.value.length > 0) {
      return // Don't fetch if already loaded
    }
    
    try {
      isLoading.value = true
      academicYears.value = await getAcademicYearList();
      academicYearsLoaded.value = true
    } catch (error) {
      console.error('Error fetching academic years:', error)
    } finally {
      isLoading.value = false
    }
  }

  const setCurrentPage = (page: number) => {
    currentPage.value = page
  }

  const clearCache = () => {
    articlesCache.value = {}
    articlesLoaded.value = false
  }

  const refreshArticles = async () => {
    // Force a refresh of the current data
    if (lastFetchParams.value) {
      const currentParams = { ...lastFetchParams.value }
      // Remove the entry from cache
      const cacheKey = getCacheKey(currentParams)
      delete articlesCache.value[cacheKey]
      
      // Re-fetch
      await fetchArticles(currentParams)
    }
  }

  return {
    // State
    articles,
    academicYears,
    currentPage,
    totalArticles,
    isLoading,
    articlesLoaded,
    academicYearsLoaded,
    
    // Getters
    getArticleById,
    
    // Actions
    fetchArticles,
    fetchAcademicYears,
    setCurrentPage,
    clearCache,
    refreshArticles
  }
})