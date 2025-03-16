import type { ProfilePhotoParams, ProfilePhotoResponse, User, UserDetailsParams } from "@/types/user"

import api from "./axios"

export const updateUserDetail = async (params: UserDetailsParams): Promise<User> => {
  const response = await api.patch('user/edit-detail', params);
  return response.data.user;
}

export const updateProfilePhoto = async (params: ProfilePhotoParams): Promise<ProfilePhotoResponse> => {
  const response = await api.post(`user/update-photo`, params);
  return response.data;
}