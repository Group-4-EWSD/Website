import axios from 'axios'
import { useCookies } from 'vue3-cookies'

import { forceSignOut } from '@/lib/utils'

// const BASE_URL = 'https://ewsd-project.vercel.app/v1/'
const BASE_URL = 'http://127.0.0.1:8000/api/'

// Create Axios instance
const api = axios.create({
  baseURL: BASE_URL,
  withCredentials: false, // Allows sending cookies with requests
  headers: {
    'Content-Type': 'multipart/form-data',
  },
})

// Request interceptor to attach token automatically
api.interceptors.request.use(
  (config) => {
    const { cookies } = useCookies()
    const token = cookies.get('token')

    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => Promise.reject(error),
)

// Response interceptor to handle 401 errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    // Check if error is 401 Unauthorized
    if (error.response && error.response.status === 401) {
      forceSignOut(false)
    }

    return Promise.reject(error)
  },
)

export default api
