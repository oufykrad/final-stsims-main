<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ListCourse;
use App\Models\Scholar;
use App\Models\SchoolCampus;
use App\Models\SchoolCourse;
use App\Models\SchoolSemester;
use App\Models\ListDropdown;
use App\Models\ListExpense;
use App\Models\ListBenefit;
use App\Models\ListProgram;
use App\Models\LocationRegion;
use App\Models\LocationProvince;
use App\Models\LocationMunicipality;
use App\Models\LocationBarangay;
use Illuminate\Http\Request;
use App\Http\Resources\DefaultResource;
use App\Http\Resources\School\SearchResource;
use App\Http\Resources\School\CourseListResource;

class ListController extends Controller
{
    public function regions()
    {
        $data = LocationRegion::orderBy('id','ASC')->get();
        return DefaultResource::collection($data);
    }

    public function provinces($id = null)
    {
        $data = LocationProvince::where('region_code',$id)->orderBy('name','ASC')->get();
        return DefaultResource::collection($data);
    }

    public function municipalities($id = null)
    {
        $data = LocationMunicipality::where('province_code',$id)->orderBy('name','ASC')->get();
        return DefaultResource::collection($data);
    }

    public function barangays($id = null)
    {
        $data = LocationBarangay::where('municipality_code',$id)->orderBy('name','ASC')->get();
        return DefaultResource::collection($data);
    }

    public function schools(Request $request){

        $keyword = $request->input('word');
        $data = SchoolCampus::with('school')
        ->whereHas('school',function ($query) use ($keyword) {
            $query->where('name', 'LIKE', '%'.$keyword.'%');
        })
        ->orWhere(function ($query) use ($keyword) {
            $query->where('campus',$keyword);
        })->get()->take(10);

        return SearchResource::collection($data);
    }

    public function courses(Request $request){
        $keyword = $request->input('word');
        $school_id = $request->input('school_id');
        $data = SchoolCourse::with('course')->where('school_id',$school_id)
        ->whereHas('course',function ($query) use ($keyword) {
            $query->where('name','LIKE','%'.$keyword.'%');
        })
        ->get()->take(10);
        // $data = ListCourse::where('name','LIKE','%'.$keyword.'%')->get()->take(10);
        return CourseListResource::collection($data);
    }

    public function subcourses($school,$course){
        $data = SchoolCourse::where('school_id',$school)->where('course_id',$course)->get();
        return $data;
    }

    public function semesteryear($id,$year){
        $data = SchoolSemester::with('semester')->where('school_id',$id)->whereYear('start_at', '>=' ,$year)->orderBy('id','DESC')->get();
        return $data;
    }
}
