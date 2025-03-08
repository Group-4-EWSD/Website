export interface ArticleParams {
  displayNumber?: number
  pageNumber?: number
  academicYearId?: string
  articleTitle?: string
  sorting?: string
  status?: number
}

export interface Articles {
  article_id: string
  article_title: string
  user_id: string
  user_name: string
  user_photo_path: string
  gender: number
  created_at: string
  updated_at: string
  file_path?: string | null
  status: number
}

export interface CountData {
  reactCount: number
  totalViewCount: number
  currentYearArticleCount: number
}

export interface ArticleData {
  article_title: string
  article_description: string
  article_type_id: string
  status: number
  article_details: File[]
}
