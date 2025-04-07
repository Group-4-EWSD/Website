import type { ArticleParams, ArticleResponse, CoordinatorArticles } from '@/types/coordinator'

import api from './axios'

export const getAllArticles = async (): Promise<ArticleResponse> => {
  const response = await api.get(`/articles`)
  return response.data
}

export const getArticles = async (params: ArticleParams): Promise<CoordinatorArticles> => {
  const response = await api.get('/articles/coordinator', { params })
  return response.data
}
