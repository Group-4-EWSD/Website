import { defineStore } from 'pinia'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

import { getDashboardData } from '@/api/manager'
import type { AuroraMember, CountData, ChartData, GuestList } from '@/types/manager'

export const useManagerStore = defineStore('manager', () => {
  const countData = ref<CountData | null>(null)
  const members = ref<AuroraMember[]>([])
  const guestList = ref<GuestList[]>([])
  const chartData = ref<ChartData[]>([])
  const prevLogin = ref('')

  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const fetchDashboardData = async () => {
    isLoading.value = true
    error.value = null

    try {
      const response = await getDashboardData()
      countData.value = response.countData
      guestList.value = response.guestList
      prevLogin.value = response.prev_login
      chartData.value = response.articlesPerYear
      members.value = response.memberList
    } catch (error: any) {
      toast.error(error.response?.data?.message || 'An unexpected error occurred.')
      console.error('Error fetching dashboard data:', error)
      error.value = 'Failed to load data. Please try again.'
    } finally {
      isLoading.value = false
    }
  }

  function reset() {
    countData.value = null
    guestList.value = []
    prevLogin.value = ''
    chartData.value = []
    members.value = []
    error.value = null
    isLoading.value = false
  }

  return {
    countData,
    chartData,
    guestList,
    members,
    prevLogin,
    isLoading,

    fetchDashboardData,
    reset,
  }
})
