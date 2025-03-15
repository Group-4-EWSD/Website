import api from '@/api/axios'
import type { Credentials, RegisterData } from '@/types/auth'
import { useCookies } from 'vue3-cookies'

export const handleAuthChange = (newToken: string) => {
  const { cookies } = useCookies()
  cookies.set('token', newToken)
}

export const login = async (credentials: Credentials) => {
  const formData = new FormData()

  formData.append('email', credentials.email)
  formData.append('password', credentials.password)

  return await api.post(`login`, formData)
}

export const logout = async () => {
  return await api.post(`logout`)
}

export const register = async (userData: RegisterData) => {
  return await api.post(`register`, userData)
}
