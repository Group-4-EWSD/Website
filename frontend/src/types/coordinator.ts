export interface ArticleResponse {
  countData: CountData | null
  articlesPerYear: ChartData[]
  guestList: GuestList[]
  allArticles: Article[]
  prev_login: string
  remaining_final_publish: number
}

export interface CountData {
  total_articles: number
  total_previous_articles: number
  reviewed_articles: number
  pending_articles: number
  approved_articles: number
  rejected_articles: number
  published_articles: number
  deri_participate_rate: number
  deriActiveUser: number
}

export interface ChartData {
  article_count: number
  academic_year: string
}

export interface GuestList {
  user_name: string
  user_email: string
  faculty_name: string
  phone_number: string
}

export interface Article {
  article_id: string
  article_title: string
  article_description: string
  user_photo_path: string
}

export interface ArticleParams {
  displayNumber?: number
  pageNumber?: number
  academicYearId?: string
  articleTitle?: string
  sorting?: string
  status?: number
  feedback?: number
}

export interface CoordinatorArticle {
  article_id: string
  article_title: string
  submission_date: string
  submission_deadline: string
  status: number
  article_type_name: string
  user_name: string
}

export type CoordinatorArticles = {
  totalSubmissions: number
  pendingReview: number
  approvedArticles: number
  rejectedArticles: number
  articles: CoordinatorArticle[]
}
