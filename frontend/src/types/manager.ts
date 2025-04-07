export interface DashboardParams {}

export interface DashboardResponse {
  prev_login: string
  countData: CountData
  articlesPerYear: ChartData
  guestList: GuestList[]
  memberList: AuroraMember[]
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
  article_count: []
  academic_year: []
}

export interface AuroraMember {
  user_name: string
  nickname: string
  user_email: string
  faculty_name: string
  user_type_name: string
  date_of_birth: string
  gender: number
  phone_number: string
}

export interface GuestList {
  user_name: string
  user_email: string
  faculty_name: string
  phone_number: string
}
