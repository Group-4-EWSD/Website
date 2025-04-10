import api from "./axios"
import type { SubmissionDateParams, SubmissionDate, SubmissionDateUpdateParams, ContactUsInfo } from "@/types/system-data";

export const getSubmissionDates = async (): Promise<SubmissionDate[]> => {
  const response = await api.get('system-data/list-all-system-data');
  return response.data;
}

export const createSubmissionDate = async (submissionDate: SubmissionDateParams): Promise<SubmissionDate> => {
  const response = await api.post('system-data/create', submissionDate);
  return response.data;
}

export const updateSubmissionDate = async (submissionDate: SubmissionDateUpdateParams): Promise<SubmissionDate> => {
  const response = await api.post(`system-data/update`, submissionDate);
  return response.data;
}


export const getContactUsData = async (): Promise<ContactUsInfo[]> => {
  const response = await api.get(`contact-us/get-all-list`);
  return response.data[0];
}
