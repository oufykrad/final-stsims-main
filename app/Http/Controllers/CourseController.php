<?php

namespace App\Http\Controllers;

use App\Models\ListCourse;
use Illuminate\Http\Request;
use App\Imports\CourseImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\DefaultResource;

class CourseController extends Controller
{
    public function index(Request $request){
        if($request->lists){
            $data = ListCourse::when($request->keyword, function ($query, $keyword) {
                $query->where('name', 'LIKE', '%'.$keyword.'%');
            })->paginate($request->counts);
            return DefaultResource::collection($data);
        }else{
            return inertia('Modules/Course/Index');
        }
    }

    public function store(Request $request){
        switch($request->type){
            case 'preview':
                return $this->preview($request);
            break;
            case 'import':
                return $this->import($request);
            break;
        }
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
        $data = ListCourse::all();
        return $data;
    }
}
