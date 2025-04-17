export interface DashboardResponse {
  prev_login: string
  allArticles: []
  articlesPerYear: []
  facultyList: []
  publishedList: []
}

export interface Article {
  article_id: string
  article_title: string
  article_description: string
  user_photo_path: string
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
