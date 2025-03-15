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

export interface ArticleDetail {
  article_title: string
  article_description: string
  created_at: string
  updated_at: string
  creator_name: string
  creator_gender: number
  article_status: number
  view_count: number
  like_count: number
  current_user_react: number
  comment_count: number
}

export interface ArticleResponse {
  articleDetail: ArticleDetail | null
  articleContent: any[]
  articlePhotos: Record<string, string>
  commentList: any[]
  feedbackList: any[]
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
