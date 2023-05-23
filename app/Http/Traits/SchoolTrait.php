<?php

namespace App\Http\Traits;

use App\Models\User;
use App\Models\Scholar;
use App\Models\SchoolCampus;
use App\Models\SchoolCourse;
use App\Models\SchoolSemester;
use App\Http\Resources\School\SemesterResource;
use App\Http\Resources\School\CoursesResource;
use App\Http\Resources\DefaultResource;

trait SchoolTrait { 
    
    public static function counts($id){
        $total = Scholar::whereHas('education',function ($query) use ($id) {
            $query->where('school_id',$id);
        })->count();

        $graduating = Scholar::whereHas('education',function ($query) use ($id) {
            $query->where('school_id',$id);
        })->where('status_id',36)->count();

        $ongoing = Scholar::whereHas('education',function ($query) use ($id) {
            $query->where('school_id',$id);
        })->whereIn('status_id',[31,32,33,34,35])->count();

        $array = [
            ['counts' => $total, 'name' => 'Total Scholars', 'icon' => 'ri-group-2-line', 'color' => 'success'],
            ['counts' => $graduating,'name' => 'Total Graduates', 'icon' => 'bx bxs-graduation', 'color' => 'info'],
            ['counts' => $ongoing,'name' => 'Ongoing Scholars', 'icon' => 'ri-account-circle-line', 'color' => 'primary']
        ];

        return $array;
    }

    public static function semesters($id,$counts){
        $data = SemesterResource::collection(
            SchoolSemester::query()
            ->with('semester')
            ->where('school_id',$id)
            ->orderBy('created_at','DESC')
            ->paginate($counts)
            ->withQueryString()
        );

        return $data;
    }

    public static function semester($id){
        $data = SchoolSemester::where('school_id',$id)->where('is_active',1)->first();
        return new SemesterResource($data);
    }


    public static function courses($id,$counts){
        $data = CoursesResource::collection(
            SchoolCourse::query()
            ->with('course','prospectuses')
            ->where('school_id',$id)
            ->orderBy('created_at','DESC')
            ->paginate($counts)
            ->withQueryString()
        );

        return $data;
    }
}