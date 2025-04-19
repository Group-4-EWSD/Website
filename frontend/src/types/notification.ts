export interface Notification {
  notification_id: string
  article_id: string | undefined
  article_title: string
  user_name: string
  user_type: string
  gender: string
  faculty_name: string
  message: string
  seen: number
  user_photo_path: string
  created_at: string
}
