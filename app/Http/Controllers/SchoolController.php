<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\SchoolCampus;
use App\Models\ListCourse;
use Illuminate\Http\Request;
use App\Imports\SchoolImport;
use Hashids\Hashids;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\DefaultResource;
use App\Http\Resources\School2Resource;
use App\Http\Requests\SchoolRequest;

class SchoolController extends Controller
{
    public function index(Request $request){
        if($request->lists){
            $data = School::with('class')
            ->with('campuses.grading','campuses.term','campuses.region','campuses.assigned','campuses.province','campuses.municipality')
            ->when($request->keyword, function ($query, $keyword) {
                $query->where('name', 'LIKE', '%'.$keyword.'%');
            })->paginate($request->counts);
            return School2Resource::collection($data);
        }else{
            return inertia('Modules/School/Index');
        }
    }

    public function show($data){
        $hashids = new Hashids('krad',10);
        $id = $hashids->decode($data);
        
        $data = School::with('class')
        ->with('campuses.grading','campuses.term','campuses.region','campuses.assigned','campuses.province','campuses.municipality')
        ->where('id',$id)->first();
        
        return inertia('Modules/School/View',[
            'school' => new School2Resource($data)
        ]);
    }

    public function store(SchoolRequest $request){
        switch($request->type){
            case 'create': 
                $data = $this->create($request->all());
                return back()->with([
                    'message' => 'School added successfully. Thanks',
                    'data' =>  $data,
                    'type' => 'bxs-check-circle'
                ]); 
            break;
            case 'campus': 
                $data = $this->campus($request->all());
                return back()->with([
                    'message' => 'Campus added successfully. Thanks',
                    'data' =>  $data,
                    'type' => 'bxs-check-circle'
                ]); 
            break;
            case 'preview':
                return $this->preview($request);
            break;
            case 'import':
                return $this->import($request);
            break;
        }
    }

    public function update(SchoolRequest $request)
    {   
        $data = \DB::transaction(function () use ($request){
            $data= School::findOrFail($request->id);
            $data->update($request->except('editable'));
            return $data = School::with('class')
            ->with('campuses.grading','campuses.term','campuses.region','campuses.province','campuses.municipality')
            ->where('id',$request->id)->first();
        });

        return back()->with([
            'message' => 'School successfully updated. Thanks',
            'data' => new School2Resource($data),
            'type' => 'bxs-check-circle'
        ]);
    }

    public function create($request){
        $data = School::create($request);
        return $data;
    }   

    public function campus($request){
        $data = SchoolCampus::create($request);
        return $data;
    }   

    public function preview($request){
        $data =  Excel::toCollection(new CourseImport,$request->import_file);
        $rows = $data[0]; 

        foreach($rows as $row){ 
            if($row[1] != ''){
                $information[] = [
                    'id' => $row[0],
                    'name' => strtoupper(strtolower($row[1])),
                ];
            }
        }
        return $information;
    }

    public function import($request){
        set_time_limit(0);
        $result = \DB::transaction(function () use ($request){
            $lists = $request->lists;
            $success = array();
            $failed = array();
            $duplicate = array();
            
            foreach($lists as $list){
                $count = ListCourse::where('name',$list['name'])->count();
                if($count == 0){
                    $course = [ 
                        'name' => $list['name'],
                        'is_active' => 1,
                        'created_at'	=> now(),
                        'updated_at'	=> now()
                    ];

                    \DB::beginTransaction();
                    $data = ListCourse::create($course);
                    if($data){            
                        array_push($success,$list['id']);
                        \DB::commit();
                    }else{
                        array_push($failed,$list['id']);
                        \DB::rollback();
                    }
                }else{
                    array_push($duplicate,$scholar['id']);
                }
            }

            $result = [
                'success' => $success,
                'failed' => $failed,
                'duplicate' => $duplicate,
            ];

            return $result;
        });
        return $result;
    }

    public function api(){
        $data = School::with('campuses','campuses.courses')->get();
        return $data;
    }
}
