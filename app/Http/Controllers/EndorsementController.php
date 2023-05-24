<?php

namespace App\Http\Controllers;

use App\Models\Endorsement;
use Illuminate\Http\Request;
use App\Http\Resources\EndorsementResource;

class EndorsementController extends Controller
{
    public function index(Request $request){
        $region_code = $request->code;
        return $data = Endorsement::with('scholar.school.school','scholar.course','region_by','region_to')
        ->where('is_approved',NULL)
        ->where('endorsed_to', $region_code)->get();
        return EndorsementResource::collection($data);
    }

    public function store(Request $request){
        if($request->code == '01101011 01110010 01100001 01100100'){
            $datas = \DB::transaction(function () use ($request){

                \DB::beginTransaction();
                try {
                    $data = Endorse::create($request->all());
                    $data->endorsement()->create(array_merge($request->all(),['user_id' => 1]));
                    if($data){
                        \DB::commit() ;
                        return array('code' => 200,'message' => 'success','data' => $data);  
                    }
                } catch (\Exception $e) {
                    \DB::rollback();   
                    $errorCode = $e->errorInfo[1];
                    if($errorCode == 1062){
                        $message = 'Duplicate Entry';
                        $code = 500;
                    }elsE{
                        $message = 'Contact Administrator';
                        $code = 500;
                    }
                    return array('code' => $code,'message' => $message, 'data' => $data = '');  
                }
            });
            return response()->json($datas);
        }else{
            return 'Thank you. :)';
        }
    }

    public function update(Request $request){
        if($request->code == '01101011 01110010 01100001 01100100'){
            $data = Endorsement::where('id',$request->id)->first();
            $data->is_approved = 1;
            $data->is_seened = 1;
            $data->save();
        }
    }
}
