export interface User {
  id: string
  user_name: string
  nickname: string
  user_email: string
  user_password: string
  user_type_id: string
  user_type_name: string
  faculty_id: string
  faculty_name: string
  gender: number
  date_of_birth: string | null
  phone_number: string | null
  user_photo_path: string
  user_profile_url: string
  last_login_datetime: string
}

export interface UserDetailsParams {
  user_name: string
  nickname: string
  date_of_birth: string | null
  phone_number: string | null
}

export interface ProfilePhotoParams {
  user_photo: File
}

export interface UpdatePasswordParams {
  user_password: string;
  user_password_confirmation: string;
}

export interface ProfilePhotoResponse {
  photo_path: string
}

export const GenderOptions: Record<number, string> = {
  0: 'Prefer not to say',
  1: 'Male',
  2: 'Female',
};

export interface Guest {
  guest_name: string
  email: string
  faculty: string
  phone_number: string
}

export interface Guest {
  guest_name: string
  email: string
  faculty: string
  phone_number: string
}

export interface CreateUserParams {
  user_name: string;
  nickname: string;
  user_email: string;
  user_type_id: string;
  faculty_id: string;
  gender: number;
  date_of_birth: string | null;
  phone_number: string | null;
}

export interface UpdateUserParams extends CreateUserParams {
  id: string;
}