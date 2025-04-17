import type { DashboardResponse } from '@/types/manager'

import api from './axios'

export const getDashboardData = async (): Promise<DashboardResponse> => {
  const response = await api.get(`/articles`)
  return response.data
}
