import { defineStore } from 'pinia'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

import { getArticles } from '@/api/articles'
import type { Article, ArticleParams, CountData } from '@/types/article'

export const useArticleStore = defineStore('article', () => {
  const countData = ref<CountData | null>(null)
  const articles = ref<Article[]>([])
  const currentPage = ref<number>(1)
  const displayNumber = 5
  const totalPages = ref(1)
  const sortOption = ref<string>('')
  const selectedCategory = ref('')
  const selectedYear = ref('')

  const isLoading = ref(false)
  const isFetched = ref(false)
  const error = ref<string | null>(null)

  const fetchArticles = async (params: ArticleParams = {}) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await getArticles({
        displayNumber,
        pageNumber: params.pageNumber ?? currentPage.value,
        status: 4,
        sorting: sortOption.value,
        academicYearId: selectedYear.value,
        faculty: selectedCategory.value,
      })

      countData.value = response.countData
      articles.value = response.allArticles
      isFetched.value = true
    } catch (error: any) {
      toast.error(error.response?.data?.message || 'Unknown error')
      console.error('Error fetching articles:', error)
      error.value = 'Failed to load articles. Please try again.'
    } finally {
      isLoading.value = false
    }
  }

  return {
    countData,
    articles,
    isLoading,
    currentPage,
    displayNumber,
    isFetched,
    totalPages,
    sortOption,
    selectedYear,
    selectedCategory,
    error,

    fetchArticles,
  }
})
