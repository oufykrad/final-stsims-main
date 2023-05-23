<?php

namespace App\Http\Controllers\Scholar;

use App\Models\Scholar;
use App\Models\ListAgency;
use App\Models\ListProgram;
use App\Models\ListDropdown;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Scholar\ReportResource;

class ReportController extends Controller
{
    public function generate($info){
        ini_set('memory_limit', '-1');
        $info = (!empty(json_decode($info))) ? json_decode($info) : NULL;

        $agency_id = config('app.agency');
        $agency = ListAgency::with('region')->where('id',$agency_id)->first();

     
        $data = Scholar::with('profile.address.region','profile.address.province','profile.address.municipality','profile.address.barangay','profile.user')
        ->with('program')->with('profile.user')->with('education.school.school','education.course')
        ->where('is_completed',1)
        ->where(function($query) use ($info) {  
            ($info->program == '') ? '' : $query->where('program_id',$info->program);
            ($info->status == '') ? '' : $query->where('status_id',$info->status);
            ($info->is_undergrad == '') ? '' : $query->where('is_undergrad',$info->is_undergrad);
            ($info->from != '' && $info->to != '') ? $query->whereBetween('awarded_year',[$info->from,$info->to]) : '';
        })
        ->orderBy('awarded_year','DESC')->get();

        $scholars = ReportResource::collection($data);

        $array = [
            'scholars' => $scholars,
            'agency' => $agency
        ];

        if($info->type == 'scholars'){
            $pdf = \PDF::loadView('prints.scholars',$array)->setPaper('a4', 'landscape');
            return $pdf->download('List of Scholars.pdf');
        }else if($info->type == 'graduates'){
            $pdf = \PDF::loadView('prints.graduates',$array)->setPaper('a4', 'landscape');
            return $pdf->download('List of Graduate Scholars.pdf');
        }else{
            $programs = ListProgram::where('is_sub',0)->get();
            $awards = ListDropdown::where('classification','Award')->get();
            $array['awards'] = $awards; $total_no = 0; $total_awardee = 0; 
            $totals = [];
            foreach($programs as $index=>$program){
                $data = Scholar::where('is_completed',1)->where('program_id',$program->id)->count();
                $list = [];

                foreach($awards as $index2=>$award){
                    $count = Scholar::where('is_completed',1)->where('program_id',$program->id)
                    ->whereHas('education',function ($query) use ($award) {
                        $query->where('award_id',$award->id);
                    })->count();
                    array_push($list,$count);
                }

                $array2[] = [
                    'name' => $program->name,
                    'count' => $data,
                    'list' => $list,
                    'total' => array_sum($list)
                ];
                $total_no += $data;
                $total_awardee += array_sum($list);
            }

            foreach($awards as $index2=>$award){ 
                $tot = 0;
                foreach($programs as $index=>$program){
                    $tot += $array2[$index]['list'][$index2];
                }
                array_push($totals,$tot);
            }

            $array2[] = [
                'name' => 'Total',
                'count' => $total_no,
                'list' => $totals,
                'total' => $total_awardee
            ];
            $array['scholars'] = $array2;

            $pdf = \PDF::loadView('prints.awardees',$array)->setPaper('a4', 'landscape');
            return $pdf->download('List of Graduate Scholars with Awards.pdf');
        }
    }
}
