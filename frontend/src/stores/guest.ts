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

    await getDashboardData(params)
      .then((response) => {
        articles.value = response.allArticles
        prevLogin.value = response.prev_login
        chartData.value = response.articlesPerYear
        publishedYear.value = response.publishedList
        facultyList.value = response.facultyList

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
    articles,
    chartData,
    prevLogin,
    publishedYear,
    facultyList,
    isLoading,
    error,

    fetchDashboardData,
  }
})
