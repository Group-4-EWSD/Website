import api from './axios'
import type {
  Article,
  ArticleResponse,
  ArticleParams,
  CoordinatorArticle,
} from '@/types/coordinator'

export const getAllArticles = async (): Promise<ArticleResponse> => {
  const response = await api.get(`/articles`)
  return response.data
}

export const getArticles = async (params: ArticleParams): Promise<CoordinatorArticle[]> => {
  const response = await api.get('/articles/coordinator', { params })
  return response.data
}
