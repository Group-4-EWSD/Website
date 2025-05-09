import { defineStore } from 'pinia'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

import { getArticles } from '@/api/articles'
import type { Article, ArticleParams, CountData } from '@/types/article'

export const useArticleStore = defineStore('article', () => {
  const countData = ref<CountData | null>(null)
  const articles = ref<Article[]>([])
  const prevLogin = ref('')
  const articleCount = ref<number>(0)
  const currentPage = ref<number>(1)
  const displayNumber = 5
  const totalPages = ref(1)
  const sortOption = ref<string>('')
  const categoryOptions = ref<{ label: string; value: string }[]>([])
  const yearOptions = ref<{ label: string; value: string }[]>([])
  const selectedCategory = ref('')
  const selectedYear = ref('')

  const isLoading = ref(false)
  const isFetched = ref(false)
  const hasFiltered = ref(false)
  const error = ref<string | null>(null)

  const fetchArticles = async (params: ArticleParams = {}) => {
    articles.value = []
    isLoading.value = true
    error.value = null

    try {
      const response = await getArticles({
        displayNumber,
        pageNumber: params.pageNumber ?? currentPage.value,
        status: 4,
        sorting: sortOption.value,
        academicYearId: selectedYear.value,
        facultyId: selectedCategory.value,
      })

      countData.value = response.countData
      articles.value = response.articles
      prevLogin.value = response.prev_login
      articleCount.value = response.articlesCount

      isFetched.value = true
    } catch (error: any) {
      toast.error(error.response?.data?.message || 'Unknown error')
      console.error('Error fetching articles:', error)
      error.value = 'Failed to load articles. Please try again.'
    } finally {
      isLoading.value = false
    }
  }

  const setCategoryOptions = (options: { label: string; value: string }[]) => {
    categoryOptions.value = options
  }

  const setYearOptions = (options: { label: string; value: string }[]) => {
    yearOptions.value = options
  }

  const reset = () => {
    articles.value = []
    countData.value = null
  }

  return {
    countData,
    articles,
    prevLogin,
    articleCount,
    isLoading,
    currentPage,
    displayNumber,
    isFetched,
    hasFiltered,
    totalPages,
    sortOption,
    selectedYear,
    selectedCategory,
    categoryOptions,
    yearOptions,
    error,

    fetchArticles,
    setCategoryOptions,
    setYearOptions,
    reset,
  }
})
