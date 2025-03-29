import api from '../axios'
import type { ArticleResponse } from '@/types/article'

export const getArticles = async (): Promise<ArticleResponse> => {
  const response = await api.get(`coordinator/articles`)
  return response.data
}
