import api from '@/api/axios'

export const getNotifications = async () => {
  return await api.get(`notifications`)
}

export const changeNotiStatus = async (noti: string) => {
  return await api.post(`notifications`, { notification_id: noti })
}
