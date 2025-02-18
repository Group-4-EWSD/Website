import api from '@/api/axios'
import type { Credentials, RegisterData } from '@/types/auth'

export const login = async (credentials: Credentials) => {
  return await api.post(`login`, credentials)
}

export const register = async (userData: RegisterData) => {
  return await api.post(`register`, userData)
}

// export const logout = async () => {
//     const token = localStorage.getItem("token");

//     await axios.post(
//       `${API_URL}/logout`,
//       {},
//       {
//         headers: { Authorization: `Bearer ${token}` },
//       }
//     );
//     localStorage.removeItem("token");
// };
