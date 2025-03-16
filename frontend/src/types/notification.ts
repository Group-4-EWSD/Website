export type NotificationList = Notification[]

export interface Notification {
  action_id: string
  action_type: string
  article_id: string | undefined
  user_name: string
  user_type_name: string
  gender: string
  faculty_name: string
  message: string
  seen: number
  user_photo_path: string
  created_at: string
}
