<?php

namespace App\Http\Controllers;

use App\Models\School;
use Hashids\Hashids;
use App\Models\ListAgency;
use App\Models\SchoolCampus;
use App\Models\SchoolCourse;
use App\Models\SchoolSemester;
use App\Models\SchoolCourseProspectus;
use Illuminate\Http\Request;
use App\Http\Resources\DefaultResource;
use App\Http\Resources\School\IndexResource;
use App\Http\Resources\School\CourseResource;
use App\Http\Requests\SchoolProfileRequest;
use App\Http\Traits\SchoolTrait;

class SchoolController extends Controller
{
    use SchoolTrait; 

    public function index(Request $request){
        if($request->search){
            $data = IndexResource::collection(
                SchoolCampus::query()
                ->with('school','semesters.semester','courses')
                ->when($request->region, function ($query, $region) {
                    $query->whereHas('municipality',function ($query) use ($region) {
                        $query->whereHas('province',function ($query) use ($region) {
                            $query->whereHas('region',function ($query) use ($region) {
                                $query->where('id',$region);
                            });
                        });
                    });
                })
                ->when($request->keyword, function ($query, $keyword) {
                    $query->whereHas('school',function ($query) use ($keyword) {
                        $query->where('name', 'LIKE', '%'.$keyword.'%');
                    })->orWhere(function ($query) use ($keyword) {
                        $query->where('campus', 'LIKE', '%'.$keyword.'%');
                    });
                })
                ->whereHas('school',function ($query) {
                    $query->orderBy('name','ASC');
                })
                ->paginate(10)
                ->withQueryString()
            );
            return $data;
        }else{
            $agency_id = config('app.agency');
            $agency = ListAgency::with('region')->where('id',$agency_id)->first();
            $region = $agency->region_code;

            $count = SchoolCampus::query()
            ->whereHas('municipality',function ($query) use ($region) {
                $query->whereHas('province',function ($query) use ($region) {
                    $query->whereHas('region',function ($query) use ($region) {
                        $query->where('code',$region);
                    });
                });
            })->count();

            return inertia('Modules/Schools/Index',['agency' => $agency, 'count' => $count]);
        }
    }

    public function show($id)
    {
        $hashids = new Hashids('krad',10);
        $id = $hashids->decode($id);
        
        $data = new IndexResource(
            SchoolCampus::with('school')
            ->with('courses.course','courses.prospectuses')
            ->with('semesters.semester')
            ->where('id',$id[0])->first()
        );

        return inertia('Modules/Schools/View',[
            'school' => $data
        ]);
    }

    public function edit($id,Request $request){
        $data = \DB::transaction(function () use ($id,$request){
            $type = $request->type;
            switch($type){
                case 'counts':
                    return $this->counts($id);
                break;
                case 'semesters':
                    return $this->semesters($id,$request->counts);
                break;
                case 'semester':
                    return $this->semester($id);
                break;
                case 'courses':
                    return $this->courses($id,$request->counts);
                break;
            }
        });
        return $data;
    }

    public function store(SchoolProfileRequest $request){
        switch($request->option){
            case 'semester': 
                $message = 'Semester successfully created. Thanks';
                $data = new DefaultResource(SchoolSemester::create($request->all()));
                $update = SchoolSemester::where('id','!=',$data->id)->where('school_id',$data->school_id)->update(['is_active' => false]);
            break;
            case 'course': 
                $message = 'Course successfully added. Thanks';
                $data = new CourseResource(SchoolCourse::create($request->all()));
            break;
            case 'prospectus': 
                $level = ['First Year','Second Year','Third Year','Fourth Year','Fifth Year'];
                $semester = ['First Semester', 'Second Semester', 'Summer Class'];
                $trimester = ['First Term', 'Second Term', 'Third Term', 'Midyear'];
                $quarter = ['First Term', 'Second Term', 'Third Term','Fourth Term'];
                $years = $request->years;
                $type = $request->type;
                if($type == 'Semester'){
                    $semesters = $semester;
                }else  if($type == 'Trimester'){
                    $semesters = $trimester;
                }else{
                    $semesters = $quarter;
                }
                $group = []; $courses = [];

                for ($i = 0; $i < $years; ++$i) {
                    $sem = [];
                    foreach($semesters as $key=>$semester){
                        $sem[] = ['semester' => $semester,'total' => 0,'courses' => $courses];
                    }
                    $group[] = ['year_level' => $level[$i],'semesters' => $sem];
                }
                $request['subjects'] = json_encode($group,true);
                $request['added_by'] = \Auth::user()->id;
                $data = SchoolCourseProspectus::create($request->all());
                $data = SchoolCourseProspectus::where('id',$data->id)->first();
                $message = 'Prospectus successfully added. Thanks';
            break;
        }
        
        return back()->with([
            'data' => $data,
            'message' => $message,
            'type' => 'bxs-check-circle'
        ]);
    }

    public function update(Request $request){
        $data = SchoolCourseProspectus::where('id',$request->id)->first();
        $data->update($request->except('editable'));
        $message = 'Prospectus successfully updated. Thanks';
        
        return back()->with([
            'data' => $data,
            'message' => $message,
            'type' => 'bxs-check-circle'
        ]);
    }
}
