import api from '@/api/axios'
import type { Articles, articleParams, CountData } from '@/types/article'

interface ArticlesResponse {
  countData: CountData
  allArticles: Articles[]
}

export const getArticles = async (params: articleParams): Promise<ArticlesResponse> => {
  const response = await api.get(`articles`, { params })
  return response.data
}

export const getArticleDetails = async (articleId: string) => {
  return await api.get(`articles/${articleId}`)
}
