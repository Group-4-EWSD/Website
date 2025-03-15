import api from '@/api/axios'

export const getNotifications = async () => {
  return await api.get(`getNotifications`)
}
