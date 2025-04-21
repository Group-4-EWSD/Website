import { defineStore } from 'pinia'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

import { getDashboardData } from '@/api/guest'
import type {
  ChartData,
  facultyList,
  GuestArticle,
  GuestParams,
  publishedYear,
} from '@/types/guest'

export const useGuestStore = defineStore('guest', () => {
  const articles = ref<GuestArticle[]>([])
  const chartData = ref<ChartData[]>([])
  const publishedYear = ref<publishedYear[]>([])
  const facultyList = ref<facultyList[]>([])
  const prevLogin = ref('')
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const fetchDashboardData = async (params?: { facultyId?: string; academicYearId?: string }) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await getDashboardData(params)

      articles.value = response.allArticles
      prevLogin.value = response.prev_login
      chartData.value = response.articlesPerYear
      publishedYear.value = response.publishedList
      facultyList.value = response.facultyList
    } catch (err: any) {
      console.error('Error fetching dashboard data:', err)
      toast.error(err.response?.data?.message || 'Something went wrong.')
      error.value = 'Failed to load articles. Please try again.'
    } finally {
      isLoading.value = false
    }
  }

  function reset() {
    articles.value = []
    prevLogin.value = ''
    chartData.value = []
    publishedYear.value = []
    facultyList.value = []
    error.value = null
    isLoading.value = false
  }

  return {
    articles,
    chartData,
    prevLogin,
    publishedYear,
    facultyList,
    isLoading,
    error,

    fetchDashboardData,
    reset,
  }
})
