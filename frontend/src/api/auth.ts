import api from '@/api/axios'
import type { Credentials, RegisterData } from '@/types/auth'

export const login = async (credentials: Credentials) => {

  const formData = new FormData()

  formData.append('email', credentials.email)
  formData.append('password', credentials.password)

  return await api.post(`login`, formData)
}

export const logout = async () => {
  return await api.post(`logout`)
}
