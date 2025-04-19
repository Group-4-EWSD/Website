import type { DashboardResponse, GuestParams } from '@/types/guest'
import api from './axios'

export const getDashboardData = async (params?: GuestParams): Promise<DashboardResponse> => {
  const response = await api.get(`/articles`, { params })
  return response.data
}
