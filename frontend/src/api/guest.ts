import type { DashboardResponse } from '@/types/guest'

import api from './axios'

export const getDashboardData = async (): Promise<DashboardResponse> => {
  const response = await api.get(`/articles`)
  return response.data
}
