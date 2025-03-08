import api from '@/api/axios'
import type { Articles, ArticleParams, CountData, ArticleData } from '@/types/article'

interface ArticlesResponse {
  countData: CountData
  allArticles: Articles[]
}

export const ArticleStatus = {
  DRAFT: 0,
  SUBMITTED: 1
}

export const getArticles = async (params: ArticleParams): Promise<ArticlesResponse> => {
  const response = await api.get(`articles`, { params })
  return response.data
}

export const getMyArticles = async (params: ArticleParams): Promise<ArticlesResponse> => {
  const response = await api.get(`articles/my-articles`, { params })
  return response.data
}

export const getArticleDetails = async (articleId: string) => {
  return await api.get(`articles/${articleId}`)
}

export const uploadArticle = async (article: ArticleData) => {

  const formData = new FormData()

  formData.append('article_title', article.article_title)
  formData.append('article_description', article.article_description)
  // formData.append('article_type_id', article.article_type_id)
  formData.append('article_type_id', 'fd67f6dd-2df6-4094-a334-d493fc301579')
  formData.append('status', article.status.toString())

  // Properly append files to the article_details[] array
  if (Array.isArray(article.article_details)) {
    article.article_details.forEach((file: File) => {
      formData.append('article_details[]', file)
    })
  }

  return await api.post(`articles/create`, formData)
}
