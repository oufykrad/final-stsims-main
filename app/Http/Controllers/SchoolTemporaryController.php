<?php

namespace App\Http\Controllers;

use App\Models\SchoolTemp;
use App\Models\SchoolCampusTemp;
use Illuminate\Http\Request;
use App\Http\Requests\SchoolTempRequest;
use App\Http\Resources\SchoolListResource;

class SchoolTemporaryController extends Controller
{
    public function index(){
        return inertia('Modules/School/Temp/Index');
    }

    public function store(Request $request){
       $region_code = \Auth::user()->profile->agency->region_code;
        if($request->new == 'false'){
            $data = SchoolTemp::findOrFail($request->id);
            $data->campuses()->create(array_merge($request->all(),['assigned_region' => $region_code]));
            $id = \DB::getPdo()->lastInsertId();
            $data = new SchoolListResource(SchoolCampusTemp::findOrFail($id));
        }else{
            $data = SchoolTemp::create($request->all());
            $data->campuses()->create(array_merge($request->all(),['assigned_region' => $region_code]));
            $id = \DB::getPdo()->lastInsertId();
            $data = new SchoolListResource(SchoolCampusTemp::findOrFail($id));
        }

        return back()->with([
            'message' => 'School created successfully. Thanks',
            'data' => $data,
            'type' => 'bxs-check-circle'
        ]); 
    }
}
