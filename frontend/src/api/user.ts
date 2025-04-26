import type { CreateUserParams, ProfilePhotoParams, ProfilePhotoResponse, UpdatePasswordParams, UpdateUserParams, User, UserDetailsParams } from "@/types/user"

import api from "./axios"

export const updateUserDetail = async (params: UserDetailsParams): Promise<User> => {
  const response = await api.post('user/edit-detail', params);
  return response.data.updated_user;
}

export const updateProfilePhoto = async (params: ProfilePhotoParams): Promise<ProfilePhotoResponse> => {
  const response = await api.post(`user/update-photo`, params);
  return response.data;
}

export const updateUserPassword = async (params: UpdatePasswordParams) => {
  const response = await api.post(`user/update-password`, params);
  return response.data;
}

export const deleteProfilePhoto = async () => {
  return await api.delete(`user/delete-photo`);
}

export const getAllUsers = async (): Promise<User[]> => {
  const response = await api.get('all-user-list');
  return response.data;
}

export const createUser = async (user: CreateUserParams): Promise<User> => {
  const response = await api.post('user-create', user);
  return response.data;
}

export const updateUser = async (user: UpdateUserParams): Promise<User> => {
  const response = await api.post('user-update', user);
  return response.data;
}

export const forcePasswordReset = async (userId: string): Promise<User> => {
  const response = await api.post(`password-reset`, { user_id : userId });
  return response.data;
}