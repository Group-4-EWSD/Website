export interface User {
  id: string
  user_name: string
  nickname: string
  user_email: string
  user_type_id: string
  user_type_name: string
  faculty_name: string
  gender: number
  date_of_birth: string | null
  phone_number: string | null
  user_photo_path: string
}
