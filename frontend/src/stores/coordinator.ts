import { defineStore } from 'pinia'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

import { getAllArticles, getArticles } from '@/api/coordinator'
import type {
  Article,
  ArticleParams,
  ChartData,
  CoordinatorArticles,
  CountData,
  GuestList,
} from '@/types/coordinator'

export const useCoordinatorStore = defineStore('coordinator-article', () => {
  const countData = ref<CountData | null>(null)
  const articles = ref<Article[]>([])
  const guestList = ref<GuestList[]>([])
  const chartData = ref<ChartData[]>([])
  const prevLogin = ref('')
  const publicDate = ref(0)

  const coordinatorArticles = ref<CoordinatorArticles>({
    totalSubmissions: 0,
    pendingReview: 0,
    approvedArticles: 0,
    rejectedArticles: 0,
    articles: [],
  })

  const approvedArticles = ref<CoordinatorArticles>({
    totalSubmissions: 0,
    pendingReview: 0,
    approvedArticles: 0,
    rejectedArticles: 0,
    articles: [],
  })

  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const fetchAllArticles = async () => {
    isLoading.value = true
    error.value = null

    await getAllArticles()
      .then((response) => {
        countData.value = response.countData
        articles.value = response.allArticles
        guestList.value = response.guestList
        prevLogin.value = response.prev_login
        publicDate.value = response.remaining_final_publish
        chartData.value = response.articlesPerYear

        isLoading.value = false
      })
      .catch((error) => {
        isLoading.value = false
        toast.error(error.response.data.message)
        console.error('Error fetching articles:', error)
        error.value = 'Failed to load articles. Please try again.'
      })
  }

  const fetchCoordinatorArticles = async (params: ArticleParams = {}) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await getArticles(params)

      coordinatorArticles.value = {
        totalSubmissions: response.totalSubmissions,
        pendingReview: response.pendingReview,
        approvedArticles: response.approvedArticles,
        rejectedArticles: response.rejectedArticles,
        articles: response.articles,
      }
    } catch (err: any) {
      toast.error(err.response?.data?.message || 'Error fetching articles')
      console.error('Error fetching articles:', err)
      error.value = 'Failed to load articles. Please try again.'
    } finally {
      isLoading.value = false
    }
  }

  const fetchApprovedArticles = async () => {
    isLoading.value = true
    error.value = null

    await getArticles({ status: 2 })
      .then((response) => {
        approvedArticles.value = {
          totalSubmissions: response.totalSubmissions,
          pendingReview: response.pendingReview,
          approvedArticles: response.approvedArticles,
          rejectedArticles: response.approvedArticles,
          articles: response.articles,
        }

        isLoading.value = false
      })
      .catch((error) => {
        isLoading.value = false
        toast.error(error.response.data.message)
        console.error('Error fetching articles:', error)
        error.value = 'Failed to load articles. Please try again.'
      })
  }

  return {
    countData,
    chartData,
    guestList,
    articles,
    prevLogin,
    publicDate,
    isLoading,
    coordinatorArticles,
    approvedArticles,

    fetchAllArticles,
    fetchCoordinatorArticles,
    fetchApprovedArticles,
  }
})
