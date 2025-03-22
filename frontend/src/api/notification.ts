import api from '@/api/axios'

export interface actionParams {
  actionId: string | null
  actionType: number
}

export const getNotifications = async () => {
  return await api.get(`notifications`)
}

export const createComment = async (params: actionParams) => {
  return await api.post(`notifications`, params)
}
