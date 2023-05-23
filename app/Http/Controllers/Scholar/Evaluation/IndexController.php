<?php

namespace App\Http\Controllers\Scholar\Evaluation;

use App\Models\EnrolledList;
use App\Models\ScholarEducation;
use App\Models\ScholarEnrollment;
use App\Models\ScholarEnrollmentList;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\UploadTrait;
use App\Http\Requests\GradeRequest;
use App\Http\Resources\Evaluation\EnrollmentResource;

class IndexController extends Controller
{
    use UploadTrait;

    public function store(GradeRequest $request)
    {      
        if($request->editable){
            $data = $this->lock($request);
            $message = 'Scholar grades locked successfully. Thanks';
        }else{
            $data = $this->save($request);
            $message = 'Scholar grades updated successfully. Thanks';
        }
        return back()->with([
            'message' => $message,
            'data' =>  new EnrollmentResource($data),
            'type' => 'bxs-check-circle'
        ]); 
    }

    public function save($request){
        $enrollment = ScholarEnrollment::where('id',$request->enrollment_id)->first();
        $attach = json_decode($enrollment->attachment,true);
        $count = count($attach['grades']);
        
        $attach['grades'] = array_merge($attach['grades'],$this->grade($request,$enrollment,$count));
        // array_push($attach['grades'],$this->grade($request,$enrollment,$count));
        $enrollment->attachment = $attach;
        $enrollment->save();
        
        $scholar_id = $enrollment->scholar_id;
        $semester_id = $enrollment->semester_id;

        $count = 0;
        $subjects = json_decode($request->lists,true);
        foreach($subjects as $list){
            $enrollment_id = $list['enrollment_id'];
            ($list['grade'] == null) ? $count++ : '';
            $data = ScholarEnrollmentList::where('id',$list['id'])->first();
            $data->grade = $list['grade'];
            $data->is_failed = ($list['grade']== 'F' || $list['grade'] == 5) ? 1 : 0;
            if($data->isDirty('grade')){
                $data->encoded_by = \Auth::user()->id;
            }
            $data->save();
        }

        $data = ScholarEnrollment::where('id',$enrollment_id)->first();
        if($count == 0){
            $data->is_clear = 1;
            $p = EnrolledList::where('scholar_id',$scholar_id)->where('school_semester_id',$semester_id)->update(['grades_completed' => 1]);
        }else{ 
            $data->is_clear = 0;
        }
        $data->save();

        return $data;
    }

    public function lock($request){
        $data = ScholarEnrollment::where('id',$request->id)->first();
        $data->update($request->except('editable'));
        if($data->is_clear){
            $scholar_id =  $data->scholar_id;
            $semester_id =  $data->semester_id;

            $p = EnrolledList::where('scholar_id',$scholar_id)->where('school_semester_id',$semester_id)->update(['grades_completed' => 1]);

            $p = ScholarEducation::with('subcourse')->where('scholar_id',$scholar_id)->first();
            $years = $p->subcourse->years; 
            $semesters = 3;

            $prospectus = json_decode($p->information,true);

            for($x = 0; $x < $years; $x++){
                for($y = 0; $y < $semesters; $y++){
                    foreach($request->lists as $list){
                        $code = $list['code']; $is_lab = $list['is_lab'];
                        $results = array_filter($prospectus['prospectus'][$x]['semesters'][$y]['courses'], function($people) use ($code,$is_lab) {
                            if(array_key_exists("code",$people)){
                                return $people['code'] == $code && $people['is_lab'] == $is_lab;
                            }else{
                                return $results = [];
                            }
                        });
                        if(!empty($results)){
                            $key = array_keys($results)[0];
                            if($list['grade'] != null){
                                ($list['grade'] == 'F' || $list['grade'] == 'f' || $list['grade'] == 5) ? $failed = 1 : $failed = 0;
                                if(array_key_exists("is_failed",$prospectus['prospectus'][$x]['semesters'][$y]['courses'][$key])){
                                    if($prospectus['prospectus'][$x]['semesters'][$y]['courses'][$key]['is_failed']){
                                        $grades = $prospectus['prospectus'][$x]['semesters'][$y]['courses'][$key]['grades'];
                                        array_push($grades,strtoupper($list['grade']));
                                        $prospectus['prospectus'][$x]['semesters'][$y]['courses'][$key]['grades'] = $grades;
                                    }
                                }else{
                                    if($failed){
                                        $grades = [];
                                        array_push($grades,strtoupper($list['grade']));
                                        $prospectus['prospectus'][$x]['semesters'][$y]['courses'][$key]['grades'] = $grades;
                                    }else{
                                        $prospectus['prospectus'][$x]['semesters'][$y]['courses'][$key]['grade'] = $list['grade'];
                                    }
                                }
                                $prospectus['prospectus'][$x]['semesters'][$y]['courses'][$key]['is_failed'] = $failed;
                            }
                        }
                    }
                }
            }
            $p->information = $prospectus;
            $p->save();   
        }
        return new EnrollmentResource($data);
    }
}
