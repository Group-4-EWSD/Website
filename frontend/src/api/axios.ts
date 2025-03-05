import axios from 'axios'
import { useCookies } from 'vue3-cookies'

// const BASE_URL = 'https://ewsd-project.vercel.app/v1/'
const BASE_URL = 'http://127.0.0.1:8000/api/'

// Create Axios instance
const api = axios.create({
  baseURL: BASE_URL,
  withCredentials: true, // Allows sending cookies with requests
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

export default api
