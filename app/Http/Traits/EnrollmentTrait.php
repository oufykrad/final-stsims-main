<?php

namespace App\Http\Traits;

use Hashids\Hashids;  
use App\Models\Scholar;
use App\Models\SchoolSemester;
use App\Models\ScholarCourse;
use App\Models\ScholarEducation;
use App\Models\ScholarEnrollmentList;
use App\Models\ScholarCourseProspectus;
use App\Models\FinancialGroup;
use App\Models\FinancialGroupList;
use App\Models\Group;
use App\Models\GroupList;
use App\Models\GroupBenefit;
use App\Models\ListPrivilege;
use Carbon\Carbon;
use App\Http\Resources\DefaultResource;
use App\Models\EnrolledList;
use App\Models\BenefitList;

trait EnrollmentTrait {
   
    public function createList($id,$request) 
    {
        $lists = json_decode($request->lists,true);
        foreach($lists as $key=>$list){
            $options = [];
            ScholarEnrollmentList::create(array_merge($list,[
                'options' => json_encode($options) ,
                'enrollment_id' => $id,
                'encoded_by' =>  \Auth::user()->id
            ]));
        }
    }

    public function check($request)
    {
        $count = ScholarCourse::where('scholar_id',$request->scholar_id)->count();
        if($count == 0){
            $pros = SchoolCourseProspectus::where('school_course_id',$request->subcourse)->first();
            $information = [
                'prospectus' => json_decode($pros->subjects)
            ];

            $data = new ScholarCourse;
            $data->scholar_id = $request->scholar_id;
            $data->subcourse_id = $request->subcourse;
            $data->information = json_encode($information);
            $data->save();
        }
    }

    public function level($request){
        $hashids = new Hashids('krad',10);
        $scholar_id = $hashids->decode($request->scholar_id);
        $data = ScholarEducation::where('scholar_id',$scholar_id[0])->first();
        ($data->level_id != $request->level_id) ? $data->level_id = $request->level_id : '';
        $data->save();
    }

    public function financialGroup($request){
        $hashids = new Hashids('krad',10);
        $scholar_id = $hashids->decode($request->scholar_id);
        
        $id = $request->semester_id;
        $semester = SchoolSemester::where('id',$id)->first();

        $month = $semester->start_at;
        $semester_id = $semester->semester->id;
        $academic_year = $semester->academic_year;

        $data = Group::where('academic_year',$academic_year)->where('semester_id',$semester_id)->first();
        if(empty($data) || $data == null){
            $data = new Group;
            $data->academic_year = $academic_year;
            $data->semester_id = $semester_id;
            $data->is_regular = ($semester->semester->others == 'regular') ? 1 : 0;
            $data->save();
            //     $m = date("Y").'-'.date("m").'-1';
            //     $bm = BenefitMonth::where('month',$m)->first();
            //     if($bm == null){
            //         $bm = new BenefitMonth;
            //         $bm->month = $m;
            //         $bm->group_id = $data->id;
            //         $bm->save(); 
            //     }
            //     $month_id = $bm->id;
            // }
        }

        // $list = new FinancialGroupList;
        // $list->start_at = $month;
        // $list->group_id = $data->id;
        // $list->scholar_id = $scholar_id[0];
        // $list->save();


        //new
        
        $list = new GroupList;
        // $list->start_at = $month;
        $list->group_id = $data->id;
        $list->scholar_id = $scholar_id[0];
        $list->save();

        $list_benefits = ListPrivilege::whereNotIn('name',['Tuition & Other School Fees','Thesis Allowance','Transportation Allowance','Graduation Allowance','Group Accident Insurance'])->get();
        
        $type = ScholarEducation::with('school.term')->where('scholar_id',$scholar_id[0])->first();
        $type = $type->school->term->name;
        
        switch($type){
            case 'Semester': 
                $div = 2;
            break;
            case 'Trimester':
                $div = 3;
            break;
            case 'Quarter Term':
                $div = 4;
            break;
        }

        foreach($list_benefits as $benefit){
            $wew = [
                'benefit_id' => $benefit['id'],
                'scholar_id' => $scholar_id[0],
                'amount' => $benefit['regular_amount'] / (($benefit['type'] == 'Term') ? $div : 1),
                'release_type' => 'Full',
                'month' => $month,
                'status_id' => 55
            ];

            if($benefit['id'] == 1){
                for($x = 0; $x < 5; $x++){
                    $list = GroupBenefit::create($wew);
                    $wew['month'] = date('Y-m-d', strtotime($wew['month']. ' + 1 months'));
                }
            }else if($benefit['name'] == 'Clothing Allowance'){
                $count = GroupBenefit::where('scholar_id',$scholar_id[0])->where('benefit_id',$benefit['id'])->count();
                ($count == 0) ? $list = GroupBenefit::create($wew) : '';
            }else{
                $list = GroupBenefit::create($wew);
            }
           


            // if($benefit['id'] == 1){

               

            //     for($x = 0; $x < 5; $x++){
            //         $lists = new GroupBenefit;
            //         $lists->benefit_id = $benefit['id'];
            //         $lists->scholar_id = $scholar_id[0];
            //         $lists->amount = $benefit['amount'];
            //         $lists->release_type = 'Full';
            //         $lists->month = $month;
            //         $lists->group_id = $data->id;
            //         $lists->status_id = 49;
            //         $lists->save();
            //         $month = date('Y-m-d', strtotime($month. ' + 1 months'));
            //     }
            // }else{
            //     if($benefit['name'] == 'Clothing'){
            //         $count = GroupBenefit::where('scholar_id',$scholar_id[0])->where('benefit_id',$benefit['id'])->count();

            //         if($count == 0){
            //             $lists = new GroupBenefit;
            //             $lists->benefit_id = $benefit['id'];
            //             $lists->scholar_id = $scholar_id[0];
            //             $lists->amount = $benefit['amount'];
            //             $lists->release_type = 'Full';
            //             $lists->month = $month;
            //             $lists->group_id = $data->id;
            //             $lists->status_id = 49;
            //             $lists->save();
            //         }
            //     }
               
            // }
        }
    }

    public function newFinancialGroup($request){
        $hashids = new Hashids('krad',10);
        $scholar_id = $hashids->decode($request->scholar_id);

        $semester_id = $request->semester_id;
        $semester = SchoolSemester::where('id',$semester_id)->first();

        $month = $semester->start_at;

        $data = new EnrolledList;
        $data->school_semester_id = $semester_id;
        $data->scholar_id = $scholar_id[0];
        if($data->save()){
            $list_benefits = ListPrivilege::whereNotIn('name',['Tuition & Other School Fees','Thesis Allowance','Transportation Allowance','Graduation Allowance','Group Accident Insurance', 'Others'])->get();
        
            $type = ScholarEducation::with('school.term')->where('scholar_id',$scholar_id[0])->first();
            $type = $type->school->term->name;

            switch($type){
                case 'Semester': 
                    $div = 2;
                break;
                case 'Trimester':
                    $div = 3;
                break;
                case 'Quarter Term':
                    $div = 4;
                break;
            }
    
            foreach($list_benefits as $benefit){
                $attachment = [];
                $wew = [
                    'benefit_id' => $benefit['id'],
                    'scholar_id' => $scholar_id[0],
                    'amount' => $benefit['regular_amount'] / (($benefit['type'] == 'Term') ? $div : 1),
                    'release_type' => 'Full',
                    'month' => $month,
                    'status_id' => 55,
                    'school_semester_id' => $semester_id,
                    'attachment' => json_encode($attachment)
                ];
    
                if($benefit['id'] == 1){
                    for($x = 0; $x < 5; $x++){
                        $list = BenefitList::create($wew);
                        $wew['month'] = date('Y-m-d', strtotime($wew['month']. ' + 1 months'));
                    }
                }else if($benefit['name'] == 'Clothing Allowance'){
                    $count = BenefitList::where('scholar_id',$scholar_id[0])->where('benefit_id',$benefit['id'])->count();
                    ($count == 0) ? $list = BenefitList::create($wew) : '';
                }else{
                    $list = BenefitList::create($wew);
                }
            }
        }
    }
}