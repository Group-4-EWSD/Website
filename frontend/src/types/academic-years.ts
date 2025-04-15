export interface AcademicYear {
  academic_year_id: string;
  academic_year_description: string;
  updated_at: string;
}

export interface AcademicYearParams {
  academic_year_start: string;
  academic_year_end: string;
}

export interface AcademicYearUpdateParams extends AcademicYearParams {
  academic_year_id: string;
  updated_at: string;
}