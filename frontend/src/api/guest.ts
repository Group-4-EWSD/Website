import type { ArticleResponse, DashboardResponse, GuestParams } from '@/types/guest'

import api from './axios'

export const getDashboardData = async (params?: GuestParams): Promise<DashboardResponse> => {
  const response = await api.get(`/articles`, { params })
  return response.data
}

export const getArticles = async (params?: GuestParams): Promise<ArticleResponse> => {
  const response = await api.get(`/articles/guest`, { params })
  return response.data
}
