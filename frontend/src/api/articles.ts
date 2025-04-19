import api from '@/api/axios'
import type {
  Article,
  ArticleData,
  ArticleParams,
  Category,
  CountData,
  DraftArticle,
  MyArticlesResponse,
  UpdateArcitleData,
} from '@/types/article'

interface ArticlesResponse {
  countData: CountData
  allArticles: Article[]
}

export enum ArticleStatus {
  DRAFT = 0,
  PENDING = 1,
  APPROVED = 2,
  REJECTED = 3,
  PUBLISHED = 4,
}

export interface ActionParams {
  articleId: string | null
  message: string
}

export interface Comment {
  id: number
  user_name: string
  message: string
  created_at: string
}

export const getArticles = async (params: ArticleParams): Promise<ArticlesResponse> => {
  const response = await api.get(`articles`, { params })
  return response.data
}

export const getMyArticles = async (params: ArticleParams): Promise<MyArticlesResponse> => {
  const response = await api.get(`articles/my-articles`, { params })
  return response.data
}

export const getDraftArticles = async (): Promise<DraftArticle[]> => {
  const response = await api.get('articles/draft-list')
  return response.data
}

export const getArticleDetails = async (articleId: string) => {
  return await api.get(`articles/${articleId}`)
}

export const createComment = async (params: ActionParams) => {
  return await api.post(`/articles/comment`, params)
}

export const createFeedback = async (params: ActionParams) => {
  return await api.post(`/articles/feedback`, params)
}

export const uploadArticle = async (article: ArticleData) => {
  return await api.post(`articles/create`, article)
}

export const updateArticle = async (article: UpdateArcitleData) => {
  return await api.post(`articles/update`, article)
}

export const getCategories = async (): Promise<Category[]> => {
  const response = await api.get('item-list?item=1')
  return response.data
}

export const updateStatus = async (status: number, articleId: string) => {
  return await api.post(`articles/change-status/${articleId}`, { status })
}

export const downloadArticles = async (articleIdList: string[]) => {
  return await api.get('articles/download', {
    params: { articleIdList },
  })
}

export const getFilterItems = async (item: number) => {
  const { data } = await api.get('item-list', { params: { item } })
  return data
}

export const updateReact = async (articleId: string) => {
  return await api.post(`articles/like`, { articleId })
}
