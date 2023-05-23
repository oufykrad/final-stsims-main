<?php

namespace App\Http\Traits;

use App\Models\User;
use App\Models\Scholar;
use App\Models\Profile;
use App\Models\Qualifier;
use App\Models\ScholarStatus;
use App\Models\SchoolCourseProspectus;
use App\Models\ScholarEducation;
use App\Models\ProfileAddress;
use App\Http\Resources\Scholar\IndexResource;
use App\Http\Resources\Scholar\EvaluationResource;

trait ScholarsTrait { //Storing Scholar
    
    public static function information($request){
        $data = Scholar::with('profile')->with('education.school')->where('id',$request->id)->first();
        if($request->type == 'old'){
            $request['is_completed'] = 1;
            $data->education()->update($request->except('id','editable','type'));
            $data->update($request->except('editable','type'));
           
            $data = Profile::with('address.region','address.province','address.municipality','address.barangay','user')
            ->with('scholar.program')
            ->with('scholar.education.school.school','scholar.education.course')
            ->where('id',$request->id)->first();
            // $data = Scholar::with('profile')->with('profile.address.municipality.province.region')->with('education.school.school','education.course')->where('id',$request->id)->first();
        }
        return new IndexResource($data);
    }

    public static function authentication($request){
        $user = new User;
        $user->password  = bcrypt('seischolars');
        $user->role = 'Scholar';
        $user->email = $request->email;
        $user->username = 'SPAS-'.str_pad(mt_rand(1,99999999),6,'0',STR_PAD_LEFT).$user->id;
    
        if($user->save()){
            $profile = Profile::where('id',$request->id)->first();
            $profile->user_id = $user->id;
            $profile->email = $request->email;
            $profile->save();
        }
        // $data =  Scholar::with('profile.address.region','profile.address.province','profile.address.municipality','profile.address.barangay','profile.user')
        // ->with('program')->with('profile.user')->with('education.school.school','education.course')->where('profile_id',$request->id)->first();
        $data = Profile::with('address.region','address.province','address.municipality','address.barangay','user')
        ->with('scholar.program')
        ->with('scholar.education.school.school','scholar.education.course')
        ->where('id',$request->id)->first();
        return new IndexResource($data);
    }
    
    public function course($request){
        $data = ScholarEducation::where('scholar_id',$request->id)->first();

        $pros = SchoolCourseProspectus::where('school_course_id',$request->subcourse_id)->first();
        $information = [
            'prospectus' => json_decode($pros->subjects)
        ];
        $data->subcourse_id = $request->subcourse_id;
        $data->information = json_encode($information);
        if($data->save()){
            $data = Scholar::with('profile')->with('status')->with('enrollments')->with('education.school.school','education.course','education.level')
            ->where('id',$request->id)
            ->first();
            return new EvaluationResource($data);
        }   
    }
}