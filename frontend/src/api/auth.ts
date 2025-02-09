import axios from "axios";

import type { Credentials, RegisterData } from "@/types/auth";
  
const API_URL = "https://ewsd-project.vercel.app/v1"; 

export const login = async (credentials: Credentials) => {
  return await axios.post(`${API_URL}/login`, credentials);
};

export const register = async (userData: RegisterData) => {
  return await axios.post(`${API_URL}/register`, userData);
};

export const logout = async () => {
    const token = localStorage.getItem("token");

    await axios.post(
      `${API_URL}/logout`,
      {},
      {
        headers: { Authorization: `Bearer ${token}` }, 
      }
    );
    localStorage.removeItem("token"); 
};
