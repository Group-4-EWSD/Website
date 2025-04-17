export interface Faculty {
  faculty_id: string;
  faculty_name: string;
  remark: string;
  updated_at: string;
}

export interface FacultyParams {
  faculty_name: string;
  remark: string;
}

export interface FacultyUpdateParams extends FacultyParams {
  faculty_id: string;
  updated_at: string;
}