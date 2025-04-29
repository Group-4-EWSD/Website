import { defineStore } from 'pinia'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

import { getArticles, getDashboardData } from '@/api/guest'
import type {
  ChartData,
  facultyList,
  GuestArticle,
  GuestParams,
  publishedYear,
} from '@/types/guest'

export const useGuestStore = defineStore('guest', () => {
  const articles = ref<GuestArticle[]>([])
  const guestArticles = ref<GuestArticle[]>([])
  const chartData = ref<ChartData[]>([])
  const publishedYear = ref<publishedYear[]>([])
  const facultyList = ref<facultyList[]>([])
  const yearOptions = ref<{ label: string; value: string }[]>([])
  const selectedYear = ref<string>('')

  const prevLogin = ref('')
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const fetchDashboardData = async (params?: { academicYearId?: string }) => {
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

  const setYearOptions = (options: { label: string; value: string }[]) => {
    yearOptions.value = options
  }

  function setSelectedYear(value: string) {
    selectedYear.value = value
  }

  const fetchArticles = async (params?: { academicYearId?: string }) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await getArticles(params)

      guestArticles.value = response.articles
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
    guestArticles,
    yearOptions,
    selectedYear,
    isLoading,
    error,

    fetchDashboardData,
    fetchArticles,
    setYearOptions,
    setSelectedYear,
    reset,
  }
})
