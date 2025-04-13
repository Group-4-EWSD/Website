<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AcademicYearRepository
{
    public function getAllAcademicYears()
    {
        return DB::table('academic_years')
        ->select([
            'academic_year_id',
            'academic_year_description',
            'updated_at'
        ])
        ->get();
    }

    public function getAcademicYearById($academicYearId)
    {
        return DB::table('academic_years')
        ->select([
            'academic_year_id',
            'academic_year_description',
            'updated_at'
        ])
        ->where('academic_year_id', $academicYearId)
        ->first();
    }

    public function hasRelationship($academicYearId)
    {
        $hasRelations = DB::table('users')
            ->where('academic_year_id', $academicYearId)
            ->exists();

        if (!$hasRelations) {
            $hasRelations = DB::table('system_datas')
                ->where('academic_year_id', $academicYearId)
                ->exists();
        }

        return $hasRelations;
    }

    public function createAcademicYear($data)
    {
        $exists = DB::table('academic_years')
            ->where('academic_year_start', $data['academic_year_start'])
            ->where('academic_year_end', $data['academic_year_end'])
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Academic year already exists'], 409);
        }

        $academicYearId = Str::uuid()->toString();

        DB::table('academic_years')->insert([
            'academic_year_id' => $academicYearId,
            'academic_year_description' => $data['academic_year_description'],
            'academic_year_start' => $data['academic_year_start'],
            'academic_year_end' => $data['academic_year_end'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['academic_year_id' => $academicYearId], 201);
    }

    public function updateAcademicYear($academicYearId, $data)
    {
        return DB::transaction(function () use ($academicYearId, $data) {
            $existing = DB::table('academic_years')
                ->where('academic_year_id', $academicYearId)
                ->first();
    
            if (!$existing) {
                return false;
            }
    
            if ($existing->updated_at != $data['updated_at']) {
                return false;
            }
    
            $exists = DB::table('academic_years')
                ->where('academic_year_start', $data['academic_year_start'])
                ->where('academic_year_end', $data['academic_year_end'])
                ->where('academic_year_id', '!=', $academicYearId) 
                ->exists();
    
            if ($exists) {
                return false;
            }
    
            DB::table('academic_years')
                ->where('academic_year_id', $academicYearId)
                ->update([
                    'academic_year_description' => "{$data['academic_year_start']}-{$data['academic_year_end']}",
                    'academic_year_start' => $data['academic_year_start'],
                    'academic_year_end' => $data['academic_year_end'],
                    'updated_at' => now(),
                ]);

            return $this->getAcademicYearById($academicYearId);
        });
    }    
}
