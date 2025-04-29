export interface DashboardResponse {
  prev_login: string
  allArticles: []
  articlesPerYear: []
  facultyList: []
  publishedList: []
}

export interface ArticleResponse {
  articles: []
  articlesCount: Number
}

export interface GuestArticle {
  article_id: string
  article_title: string
  article_description: string
  user_photo_path: string
  submission_date: string
  submission_deadline: string
  status: number
  article_type_name: string
  user_name: string
  gender: number
}

export interface ChartData {
  article_count: number
  academic_year: string
}

export interface publishedYear {
  academic_year_start: string
}

export interface facultyList {
  faculty_id: string
  faculty_name: string
  remark: string | null
  created_at: string
  updated_at: string
  articleCount: number
}

export interface GuestParams {
  facultyId?: string
  academicYearId?: string
}
