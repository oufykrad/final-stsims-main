<?php

namespace App\Http\Controllers\Scholar;

use App\Models\Scholar;
use App\Models\Profile;
use App\Models\ListDropdown;
use App\Models\ScholarEducation;
use App\Imports\ScholarImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\CheckTrait;
use App\Http\Traits\AddressTrait;

class ImportController extends Controller
{
    use CheckTrait, AddressTrait;
    
    public function store(Request $request){
        set_time_limit(0);
        $result = \DB::transaction(function () use ($request){
            $scholars = $request->users;
            $users = array();
            $success = array();
            $failed = array();
            $duplicate = array();
            
            // foreach ($scholars as $key => $val)
            // {
            //     $names[$key] = $val['awarded_year'];
            // }
            // array_multisort($names, SORT_ASC, $scholars);

            foreach($scholars as $scholar){
                $count = Scholar::where('old_id',$scholar['id'])->count();
                if($count == 0){

                    $parents = [
                        'mother' => ($scholar['mother'] == '') ? 'n/a' : $scholar['mother'],
                        'father' => ($scholar['father'] == '') ? 'n/a' : $scholar['father'],
                    ];

                    $address = [
                        'municipality' => $scholar['municipality'],
                        'barangay' => $scholar['barangay'],
                    ];
    
                    $information = [
                        'birth_place' => 'n/a',
                        'course' => $scholar['course'],
                        'school' => $scholar['school'],
                        'address' => $address,
                        'level' => $scholar['level'],
                        'parents' => $parents
                    ];

                    $user = [ 
                        'is_completed' => 0,
                        'firstname' => $scholar['firstname'],
                        'middlename' => $scholar['middlename'],
                        'lastname' => $scholar['lastname'],
                        'suffix' => $scholar['suffix'],
                        'gender' => $scholar['gender'],
                        'information' => json_encode($information),
                        'created_at'	=> now(),
                        'updated_at'	=> now()
                    ];

                    \DB::beginTransaction();
                    $profile = Profile::create($user);

                    $course = $this->checkCourse($scholar['course']);
                    $school = $this->checkSchool($scholar['school']);

                    if($profile){            
                        $haha = [
                            'is_completed' => 0,
                            'is_undergrad' => $this->checkCategory($scholar['category']),
                            'program_id' => $this->category($scholar['category']),
                            'status_id' =>  $scholar['status']['id'],
                            'profile_id' => $profile->id,
                            'old_id' =>  $scholar['id'],
                            'awarded_year' =>  $scholar['awarded_year'],
                            'spas_id' => $scholar['spas'],
                            'account_no' => $scholar['account_no'],
                            'created_at'	=> now(),
                            'updated_at'	=> now()
                        ];
                        
                        $q = Scholar::create($haha); 
                        if($q){
                            $is_completed = 1;
                            ($course == null) ? $is_completed = 0 : '';
                            ($school == null) ? $is_completed = 0 : '';

                            $school = [
                                'scholar_id' => $q->id,
                                'course_id'=> $course,
                                'school_id'=> $school,
                                'level_id' => $this->level($scholar['level']),
                                'graduated_year' => $scholar['graduated_year'],
                                'created_at'	=> now(),
                                'updated_at'	=> now(),
                                'is_completed' => $is_completed
                            ];
                            $s = ScholarEducation::insertOrIgnore($school);
                            // $address = $this->checkAddress($scholar['municipality'],$scholar['barangay'],$q->id);

                            $address = $this->searchAddress($scholar['province'],$scholar['municipality'],$scholar['barangay'],$profile->id);
                            // if($address == null){
                            //     $address = $this->searchAddress($scholar['province'],$scholar['municipality'],$scholar['barangay'],$profile->id,0);
                            // }

                            array_push($success,$scholar['id']);
                            \DB::commit();
                        }else{
                            array_push($failed,$scholar['id']);
                            \DB::rollback();
                        }

                    }else{
                        array_push($failed,$scholar['id']);
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

    public function index(Request $request){
        $data =  Excel::toCollection(new ScholarImport,$request->import_file);
        $rows = $data[0]; 

        foreach($rows as $row){ 
            if($row[1] != ''){
                $status = ListDropdown::where('classification','Status')->where('name',$row[9])->first();

                $information[] = [
                    'id' => $row[0],
                    'firstname' => ucwords(strtolower($row[1])),
                    'middlename' => ucwords(strtolower($row[2])),
                    'lastname' => ucwords(strtolower($row[3])),
                    'suffix' => $row[4],
                    'gender' => $row[5],
                    'school' => $row[6],
                    'course' => $row[7],
                    'level' => $row[8],
                    'status' => $status,
                    'awarded_year' => $row[10],
                    'graduated_year' => $row[11],
                    'father' => $row[12],
                    'mother' => $row[13],
                    'province' => $row[14],
                    'municipality' => $row[15],
                    'barangay' => $row[16],
                    'category' => $row[17],
                    'spas' => $row[18],
                    'account_no' => $row[19],
                ];
            }
        }
        return $information;
    }
}
