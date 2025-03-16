import api from '@/api/axios'
import type { Article, ArticleParams, CountData } from '@/types/article'

interface ArticlesResponse {
  countData: CountData
  allArticles: Article[]
}

export const getArticles = async (params: ArticleParams): Promise<ArticlesResponse> => {
  const response = await api.get(`articles`, { params })
  return response.data
}

export const getArticleDetails = async (articleId: string) => {
  return await api.get(`articles/${articleId}`)
}
