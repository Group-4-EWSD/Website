import api from '@/api/axios'
import type { Credentials, RegisterData } from '@/types/auth'

export const login = async (credentials: Credentials) => {
  return await api.post(`login`, credentials)
}

export const logout = async () => {
  return await api.post(`logout`)
}

export const visitCount = async (id: string) => {
  return await api.get(`visit1111/${id}`)
}

export const register = async (user: RegisterData) => {}
