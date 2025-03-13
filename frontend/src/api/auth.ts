import api from '@/api/axios'
import type { Credentials, RegisterData } from '@/types/auth'

export const login = async (credentials: Credentials) => {
  return await api.post(`login`, credentials)
}

export const logout = async () => {
  console.log('logout api call')
  return await api.post(`logout`)
}
