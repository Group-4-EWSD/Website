export interface SubmissionDate {
  system_id: string
  system_title: string
  faculty_name: string
  academic_year_description: string
  academic_year_id: string
  faculty_id: string
  pre_submission_date: string
  actual_submission_date: string
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
}
