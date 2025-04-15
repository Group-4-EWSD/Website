export interface SubmissionDate {
  system_id: string
  system_title: string
  faculty_name: string
  academic_year_description: string
  academic_year_id: string
  faculty_id: string
  pre_submission_date: string
  actual_submission_date: string
  updated_at: string
}

export interface SubmissionDateForm {
  system_id: string
  system_title: string
  faculty_id: string
  academic_year_id: string
  pre_submission_date: string
  actual_submission_date: string
}

export interface SubmissionDateParams {
  system_title: string
  faculty_id: string
  academic_year_id: string
  pre_submission_date: string
  actual_submission_date: string
}

export interface SubmissionDateUpdateParams extends SubmissionDateParams {
  system_id: string
  updated_at: string
}

export interface ContactUsParams { 
  visitor_name: string
  visitor_email: string
  title: string
  description: string
}

export interface ContactUsInfo extends ContactUsParams { 
  contact_us_id: string
  read: number
  created_at: string
  updated_at: string
}