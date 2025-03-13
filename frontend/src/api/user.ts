import api from "./axios"

export const updateP = async (data: any) => {
  return await api.post(`user/update-photo`, data)
}