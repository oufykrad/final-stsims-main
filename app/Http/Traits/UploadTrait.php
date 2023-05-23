<?php

namespace App\Http\Traits;

use App\Models\ListDropdown;
use App\Models\SchoolSemester;

trait UploadTrait {
   
    public function enrollment($request){
        if($request->hasFile('files'))
        {   
            $level = ListDropdown::where('id',$request->level_id)->pluck('name');
            $semester = SchoolSemester::where('id',$request->semester_id)->first();
            $academic_year = $semester['academic_year'];
            $semester_name = $semester['semester']['name'];
            // dd($level[0].'-'.$academic_year.'-'.$semester_name);
            $code = $request->scholar_id;
            $files = $request->file('files');   
            foreach ($files as $key=>$file) {
                if($key == 0){
                    $file_name = $level[0].'-'.$academic_year.'-'.$semester_name.'.'.$file->getClientOriginalExtension();
                }else{
                    $file_name = $level[0].'-'.$academic_year.'-'.$semester_name.'-'.$key.'.'.$file->getClientOriginalExtension();
                }
              
                $file_path = $file->storeAs('uploads/'.$code.'/Enrollments', $file_name, 'public');
            }
            return $attachment = [
                'name' => $file_name,
                'file' => $file_path,
                'added_by' => \Auth::user()->id,
                'created_at' => date('M d, Y g:i a', strtotime(now()))
            ];
        }
    }

    public function grade($request,$enrollment,$count){
        if($request->hasFile('files'))
        {   
            $level = ListDropdown::where('id',$enrollment->level_id)->pluck('name');
            $semester = SchoolSemester::where('id',$enrollment->semester_id)->first();
            $academic_year = $semester['academic_year'];
            $semester_name = $semester['semester']['name'];
            $code = $request->scholar_id;
            
            $files = $request->file('files');   
            foreach ($files as $file) {
                if($count == 0){
                    $file_name = 'grades_'.$level[0].'-'.$academic_year.'-'.$semester_name.'.'.$file->getClientOriginalExtension();
                }else{
                    $file_name = 'grades_'.$level[0].'-'.$academic_year.'-'.$semester_name.'-'.$count.'.'.$file->getClientOriginalExtension();
                    $count++;
                }
                $file_path = $file->storeAs('uploads/'.$code.'/Grades', $file_name, 'public');

                $attachment[] = [
                    'name' => $file_name,
                    'file' => $file_path,
                    'added_by' => \Auth::user()->id,
                    'created_at' => date('M d, Y g:i a', strtotime(now()))
                ];
            }
            return $attachment;
        }
    }

    public function reimburse($request){
        if($request->hasFile('files')){
            $files = $request->file('files');   
            foreach ($files as $key=>$file) {
                if($key == 0){
                    $file_name = 'reimburse_'.date("y-m-dhis").'.'.$file->getClientOriginalExtension();
                }else{
                    $file_name = 'reimburse_'.date("y-m-dhis").'-'.$key.'.'.$file->getClientOriginalExtension();
                }
                $file_path = $file->storeAs('reimbursements', $file_name, 'public');
            }
            return $attachment = [
                'file' => $file_path,
                'added_by' => \Auth::user()->id,
                'created_at' => date('M d, Y g:i a', strtotime(now()))
            ];
        }
    }

    public function release($request){
        if($request->hasFile('attachment'))
        {   
            $id = $request->batch;
            $year = date('Y');
            $files = $request->file('attachment');   
            foreach ($files as $key=>$file) {
                if($key == 0){
                    $file_name = 'release_'.$id.'_'.$year.'.'.$file->getClientOriginalExtension();
                }else{
                    $file_name = 'release_'.$id.'_'.$year.'-'.$key.'.'.$file->getClientOriginalExtension();
                }
                $file_path = $file->storeAs('benefits', $file_name, 'public');
                
                $attachment[] = [
                    'name' => $file_name,
                    'file' => $file_path,
                    'added_by' => \Auth::user()->id,
                    'created_at' => date('M d, Y g:i a', strtotime(now()))
                ];
            }
            return $attachment;
        }
    }

    public function reimbursement($request){
        if($request->hasFile('files'))
        {   
            $code = $request->scholar_id;
            $files = $request->file('files');   
            foreach ($files as $key=>$file) {
                if($key == 0){
                    $file_name = 'reimbursement_'.date("y-m-dhis").'.'.$file->getClientOriginalExtension();
                }else{
                    $file_name = 'reimbursement_'.date("y-m-dhis").'.'.$file->getClientOriginalExtension();
                }
              
                $file_path = $file->storeAs('/reimbursements', $file_name, 'public');
            }
            return $attachment = [
                'name' => $file_name,
                'file' => $file_path,
                'added_by' => \Auth::user()->id,
                'created_at' => date('M d, Y g:i a', strtotime(now()))
            ];
        }
    }

}