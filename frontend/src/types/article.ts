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

export interface MyArticlesResponse {
  preUploadDeadline: string;
  actualUploadDeadline: string;
  latestArticles: Articles[];
  myArticles: Articles[];
}

export interface DraftArticle {
  article_title: string
  article_description: string
}

export interface Category {
  article_type_id: string,
  article_type_name: string
}