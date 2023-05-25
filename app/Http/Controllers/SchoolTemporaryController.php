<?php

namespace App\Http\Controllers;

use App\Models\SchoolTemp;
use App\Models\SchoolCampusTemp;
use Illuminate\Http\Request;
use App\Http\Requests\SchoolTempRequest;
use App\Http\Resources\SchoolListResource;

class SchoolTemporaryController extends Controller
{
    public function index(Request $request){
        if($request->type == 'lists'){
            $keyword = $request->keyword;
            $info = ''; $location = '';
            $data = SchoolListResource::collection(
                SchoolCampusTemp::with('school','school.class')
                ->with('assigned')
                ->with('municipality.province.region','term','grading')
                ->when($request->keyword, function ($query, $keyword) {
                    $query->where('campus', 'LIKE', "%{$keyword}%")
                    ->orWhereHas('school',function ($query) use ($keyword) {
                        $query->where('name', 'LIKE', "%{$keyword}%");
                    });
                })
                ->when($info, function ($query,$info) {
                    ($info->term == null) ? '' : $query->where('term_id',$info->term);
                    ($info->grading == null) ? '' : $query->where('grading_id',$info->grading);
                })
                // ->whereHas('school',function ($query) use ($keyword,$info) {
                //     $query->where(function ($query) use ($info) {
                //         ($info->class == null) ? '' : $query->where('class_id',$info->class);
                //         $query->orderBy('name','DESC');
                //     });
                //     $query->orderBy('name','DESC');
                // })
                // ->where(function ($query) use ($location) {
                //     if(!empty($location)){
                //         if(property_exists($location, 'municipality')){
                //             $query->where('municipality_code',$location->municipality);
                //         }else if(property_exists($location, 'province')){
                //             $query->whereHas('municipality',function ($query) use ($location) {
                //                 $query->whereHas('province',function ($query) use ($location) {
                //                     $query->where('province_code',$location->province);
                //                 });
                //             });
                //         }else if(property_exists($location, 'region')){
                //             $query->whereHas('municipality',function ($query) use ($location) {
                //                 $query->whereHas('province',function ($query) use ($location) {
                //                     $query->whereHas('region',function ($query) use ($location) {
                //                         $query->where('region_code',$location->region);
                //                     });
                //                 });
                //             });
                //         }
                //     }
                // })
                ->paginate($request->counts)
                ->withQueryString()
            );
            return $data;
        }else{
            return inertia('Modules/School/Temp/Index');
        }
    }

    public function store(Request $request){
       $region_code = \Auth::user()->profile->agency->region_code;
        if($request->new == 'false'){
            $data = SchoolTemp::findOrFail($request->id);
            $data->campuses()->create($request->all());
            $id = \DB::getPdo()->lastInsertId();
            $data = new SchoolListResource(SchoolCampusTemp::findOrFail($id));
        }else{
            $data = SchoolTemp::create($request->all());
            $data->campuses()->create($request->all());
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
