<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class SystemDataRepository
{
    public function listAllSysData()
    {
        return DB::table('system_datas as sd')
            ->select([
                'sd.system_id',
                'sd.system_title',
                'sd.faculty_id',
                'f.faculty_name',
                'sd.academic_year_id',
                'ay.academic_year_description',
                'sd.pre_submission_date', 
                'sd.actual_submission_date',
                'sd.updated_at'
            ])
            ->join('faculties as f', 'f.faculty_id', '=', 'sd.faculty_id')
            ->join('academic_years as ay', 'ay.academic_year_id', 'sd.academic_year_id')
            ->get(); 
    }

    public function byidSysData($SysId)
    {
        return DB::table('system_datas as sd')
            ->select([
                'sd.system_id',
                'sd.system_title',
                'sd.faculty_id',
                'f.faculty_name',
                'sd.academic_year_id',
                'ay.academic_year_description',
                'sd.pre_submission_date', 
                'sd.actual_submission_date',
                'sd.updated_at'
            ])
            ->join('faculties as f', 'f.faculty_id', '=', 'sd.faculty_id')
            ->join('academic_years as ay', 'ay.academic_year_id', 'sd.academic_year_id')
            ->where('sd.system_id','=', $SysId)
            ->get(); 
    }

    public function byFacSysData($faculty)
    {
        return DB::table('system_datas as sd')
            ->select([
                'sd.system_id',
                'sd.system_title',
                'sd.faculty_id',
                'f.faculty_name',
                'sd.academic_year_id',
                'ay.academic_year_description',
                'sd.pre_submission_date', 
                'sd.actual_submission_date',
                'sd.updated_at'
            ])
            ->join('faculties as f', 'f.faculty_id', '=', 'sd.faculty_id')
            ->join('academic_years as ay', 'ay.academic_year_id', 'sd.academic_year_id')
            ->where('sd.faculty_id','=', $faculty)
            ->get(); 
    }
 
    public function createSysData(array $data)
    {
        $createRequest = DB::table('system_datas')->insert($data);
        return $data['system_id'];
    }

    public function checkFacultyAcademicYearCombination($facultyId, $academicYearId, $excludeSystemId = null)
    {
        $query = DB::table('system_datas')
            ->where('faculty_id', $facultyId)
            ->where('academic_year_id', $academicYearId);

        if ($excludeSystemId) {
            $query->where('system_id', '!=', $excludeSystemId);
        }

        return $query->exists();
    }

    public function updateSysData($systemId, array $data)
    {
        return DB::table('system_datas')
            ->where('system_id', $systemId)
            ->update($data);
    }
    
    public function getById($systemId)
    {
        return DB::table('system_datas')
            ->where('system_id', $systemId)
            ->first();
    }
}