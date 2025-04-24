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

export const publishArticles = async (articleIdList: string[]) => {
  const response = await api.post('articles/change-status', {
    articleIdList,
    status: 3,
  })
  return response.data
}
