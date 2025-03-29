import api from "./axios"
import type { AcademicYear, AcademicYearParams, AcademicYearUpdateParams } from "@/types/academic-years";

export const getAcademicYearList = async (): Promise<AcademicYear[]> => {
  const response = await api.get('academic-years/get-all-academic-years');
  return response.data;
}

export const getAcademicYear = async (id: string): Promise<AcademicYear> => {
  const response = await api.get(`academic-years/get-all-academic-years/${id}`);
  return response.data;
}

export const createAcademicYear = async (academicYear: AcademicYearParams): Promise<AcademicYear> => {
  const response = await api.post('academic-years/create-academic-year', academicYear);
  return response.data;
}

export const updateAcademicYear = async (academicYear: AcademicYearUpdateParams): Promise<AcademicYear> => {
  const response = await api.post(`academic-years/update-academic-year`, academicYear);
  return response.data;
}
