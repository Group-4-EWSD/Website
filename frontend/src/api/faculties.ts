import type { Faculty, FacultyParams, FacultyUpdateParams } from "@/types/faculty";
import api from "./axios"

export const getFacultyList = async (): Promise<Faculty[]> => {
  const response = await api.get('faculty/get-all-faculties');
  return response.data;
}

export const getFacultyYear = async (id: string): Promise<Faculty> => {
  const response = await api.get(`faculty/get-faculty-byID/${id}`);
  return response.data;
}

export const createFaculty = async (faculty: FacultyParams): Promise<Faculty> => {
  const response = await api.post('faculty/create', faculty);
  return response.data;
}

export const updateFaculty = async (faculty: FacultyUpdateParams): Promise<Faculty> => {
  const response = await api.post(`faculty/update`, faculty);
  return response.data;
}
