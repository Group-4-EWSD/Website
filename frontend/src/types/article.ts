export interface ArticleParams {
  displayNumber?: number
  pageNumber?: number
  academicYearId?: string
  facultyId?: string
  sorting?: string
  status?: number
  articleTitle?: string
}

export type DashboardResponse = {
  countData: CountData
  articles: Article[]
  articlesCount: number
  prev_login: string
}

export interface Article {
  article_id: string
  article_title: string
  article_description: string
  article_type_id: string
  article_type_name: string
  user_id: string
  user_name: string
  user_photo_path: string
  gender: number
  created_at: string
  updated_at: string
  file_path?: string | null
  status: number
  view_count: number
  react_count: number
  comment_count: number
  current_user_react: number
  last_feedback: string
}

export interface ArticleDetail {
  article_title: string
  article_description: string
  article_type_id: string
  article_type_name: string
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

export interface UpdateArcitleData extends ArticleData {
  article_id: string
  article_remaining_files: string[]
}

export interface MyArticlesResponse {
  preUploadDeadline: string
  actualUploadDeadline: string
  latestArticles: Article[]
  myArticles: Article[]
}

export interface DraftArticle {
  article_id: string
  article_title: string
  article_description: string
  article_type_name: string
  updated_at: string
}

export interface Category {
  article_type_id: string
  article_type_name: string
}
