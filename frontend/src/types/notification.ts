export type NotificationList = Notification[]

export interface Notification {
  notification_id: number
  article_id: number
  name: string
  role: string
  faculty: string
  message: string
  seen: number
  profileImg: string
  dateTime: string
}
