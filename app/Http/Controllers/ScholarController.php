<?php

namespace App\Http\Controllers;

use Laravel\Sanctum\PersonalAccessToken;
use App\Models\Scholar;
use App\Models\ScholarAddress;
use App\Models\ScholarProfile;
use App\Models\ScholarEducation;
use App\Models\School;
use App\Models\ListStatus;
use App\Models\ListProgram;
use App\Models\ListCourse;
use App\Models\ListDropdown;
use App\Models\SchoolCampus;
use App\Imports\ScholarsImport;
use App\Models\LocationProvince;
use App\Models\LocationBarangay;
use App\Models\LocationMunicipality;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Http\Resources\Scholars\IndexResource;

class ScholarController extends Controller
{
    public function index(Request $request){
        if($request->lists){

            $info = (!empty(json_decode($request->info))) ? json_decode($request->info) : NULL;
            $education = (!empty(json_decode($request->education))) ? json_decode($request->education) : NULL;
            $location = (!empty(json_decode($request->location))) ? json_decode($request->location) : NULL;
            $keyword = $info->keyword;

            $data = IndexResource::collection(
                Scholar::
                with('address.region','address.province','address.municipality','address.barangay')
                ->with('program','subprogram','category','status')
                ->with('education.school.school','education.course')
                ->whereHas('profile',function ($query) use ($keyword) {
                    $query->when($keyword, function ($query, $keyword) {
                        $query->where(\DB::raw('concat(firstname," ",lastname)'), 'LIKE', '%'.$keyword.'%')
                        ->where(\DB::raw('concat(lastname," ",firstname)'), 'LIKE', '%'.$keyword.'%')
                        ->orWhere('spas_id','LIKE','%'.$keyword.'%');
                    });
                })
                ->whereHas('address',function ($query) use ($location) {
                    if(!empty($location)){
                        (property_exists($location, 'region')) ? $query->where('region_code',$location->region)->where('is_permanent',1) : '';
                        (property_exists($location, 'province')) ? $query->where('province_code',$location->province)->where('is_permanent',1) : '';
                        (property_exists($location, 'municipality')) ? $query->where('municipality_code',$location->municipality)->where('is_permanent',1) : '';
                        (property_exists($location, 'barangay')) ? $query->where('barangay_code',$location->barangay)->where('is_permanent',1) : '';
                    }
                })
                ->whereHas('education',function ($query) use ($education) {
                    if(!empty($education)){
                        (property_exists($education, 'school')) ? $query->where('school_id',$education->school) : '';
                        (property_exists($education, 'course')) ? $query->where('course_id',$education->course) : '';
                    }
                })
                ->where(function ($query) use ($info) {
                    ($info->program == null) ? '' : $query->where('program_id',$info->program);
                    ($info->subprogram == null) ? '' : $query->where('subprogram_id',$info->subprogram);
                    ($info->category == null) ? '' : $query->where('category_id',$info->category);
                    ($info->status == null) ? '' : $query->where('status_id',$info->status);
                    ($info->year == null) ? '' : $query->where('awarded_year',$info->year);
                 })
                ->paginate($request->counts)
                ->withQueryString()
            );
            return $data;
        }else{
            return inertia('Modules/Scholar/Index');
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
            case 'bank':
                return $this->bank($request);
            break;
            case 'bank-update':
                return $this->bank_update($request);
            break;
            case 'status':
                return $this->status2($request);
            break;
            case 'status-update':
                return $this->status2_update($request);
            break;
        }
    }

    public function preview($request){
        $data =  Excel::toCollection(new ScholarsImport,$request->import_file);
        $rows = $data[0]; 

        foreach($rows as $row){ 
            if($row[1] != ''){
                $information[] = [
                    'spas_id' => $row[0],
                    'firstname' => strtoupper(strtolower($row[1])),
                    'middlename' => strtoupper(strtolower($row[2])),
                    'lastname' => strtoupper(strtolower($row[3])),
                    'suffix' => strtoupper(strtolower($row[4])),
                    'sex' => $row[5],
                    'birthday' => $row[6],
                    'address' => strtoupper(strtolower($row[7])), strtoupper(strtolower($row[8])),
                    'barangay' => strtoupper(strtolower($row[9])),
                    'municipality' => strtoupper(strtolower($row[10])),
                    'province' => strtoupper(strtolower($row[11])),
                    'region' => strtoupper(strtolower($row[12])),
                    'district' => strtoupper(strtolower($row[13])),
                    'zipcode' => strtoupper(strtolower($row[14])),
                    'email' => strtolower($row[15]),
                    'contact_no' => strtoupper(strtolower($row[16])),
                    'year_awarded' => $row[17],
                    'program' => strtoupper(strtolower($row[18])),
                    'subprogram' => strtoupper(strtolower($row[19])),
                    'category' => strtoupper(strtolower($row[20])),
                    'schp_award' => strtoupper(strtolower($row[21])),
                    'course' => strtoupper(strtolower($row[22])),
                    'school' => strtoupper(strtolower($row[23])),
                    'status' => strtoupper(strtolower($row[25]))
                ];
            }
        }
        return $information;
    }

    public function status2($request){
        $data =  Excel::toCollection(new ScholarsImport,$request->import_file);
        $rows = $data[0]; 

        foreach($rows as $row){ 
            if($row[1] != ''){
                $information[] = [
                    'lastname' => strtoupper(strtolower($row[3])),
                    'firstname' => strtoupper(strtolower($row[1])),
                    'level' => $row[5],
                    'status' => $row[6],
                    'graduated' => $row[7],
                    'barangay' => $row[12],
                ];
            }
        }
        return $information;
    }

    public function status2_update($request){
        set_time_limit(0);
        $result = \DB::transaction(function () use ($request){
            $lists = $request->lists;
            $success = array();
            $failed = array();
            $duplicate = array();
            
            foreach($lists as $list){
                $level = $list['level'];
                $graduated = ($list['graduated']) ? $list['graduated'] : NULL;
                $status = $list['status'];
                $barangay = ($list['barangay']) ? $list['barangay'] : NULL;
                $firstname = $list['firstname'];
                $lastname = $list['lastname'];

                
                $scholar = ScholarProfile::where('firstname',$firstname)->where('lastname',$lastname)->first();
                if($status != 'Graduated' && $status != 'Terminated' && $status != 'Non-Compliance' && $status != 'Withdrawn' && $status != 'Unknown'){
                    if($scholar){
                        $scholar_id = $scholar->scholar_id;
                        
                        $status_info = ListStatus::select('id')->where('name',$status)->where('type','Ongoing')->first();
                        if($status_info){
                            $scholar = Scholar::where('id',$scholar_id)->update(['status_id' => $status_info->id]);
                           
                            $level_id = ListDropdown::where('classification','Level')->where('name',$level)->pluck('id')->first();
                            $education = ScholarEducation::where('scholar_id',$scholar_id)->update(['level_id' => $level_id]);

                            $address = ScholarAddress::where('scholar_id',$scholar_id)->first();
                            $province_code = $address->province_code;
                            $municipality_code = $address->municipality_code;

                            $b = LocationBarangay::with('municipality')->where(function($query) use ($barangay) {  
                                $query->where('name','LIKE', '%'.$barangay.'%');
                            })
                            ->when($province_code, function ($query, $province_code) {
                                $query->whereHas('municipality',function ($query) use ($province_code) {
                                    $query->whereHas('province',function ($query) use ($province_code) {
                                        $query->where('province_code',$province_code);
                                    });
                                });
                            })
                            ->first();
                            $barangay = null;
                            $district = null;
                
                            if($b != null){
                                $barangay = $b->code;
                                $district = $b->district;
                                if($municipality_code){
                                    $is_completed = 1;
                                }else{
                                    $address->municipality_code = $b->municipality->code;
                                }
                                $address->barangay_code = $b->code;
                                if($b->municipality->is_chartered){
                                    $address->district = $b->district;
                                }else{
                                    $address->district = $b->municipality->district;
                                }
                                if($barangay != null || $district != null){
                                    $address->is_completed = 1;
                                }   
                                $address->save();
                            }else{
                                $barangay = null;
                            }
                            array_push($success,$scholar_id);
                        }
                    }else{
                        array_push($failed,$firstname.' '.$lastname );
                    }
                }else if($status == 'Graduated'){
                    if($scholar){
                        $scholar_id = $scholar->scholar_id;
                        $education = ScholarEducation::where('scholar_id',$scholar_id)->update(['graduated_year' => $graduated]);
                        array_push($success,$scholar_id);
                    }
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

    public function bank($request){
        $data =  Excel::toCollection(new ScholarsImport,$request->import_file);
        $rows = $data[0]; 

        foreach($rows as $row){ 
            if($row[1] != ''){
                $information[] = [
                    'account_no' => $row[0],
                    'lastname' => strtoupper(strtolower($row[1])),
                    'firstname' => strtoupper(strtolower($row[2])),
                ];
            }
        }
        return $information;
    }

    public function bank_update($request){
        set_time_limit(0);
        $result = \DB::transaction(function () use ($request){
            $lists = $request->lists;
            $success = array();
            $failed = array();
            $duplicate = array();
            
            foreach($lists as $list){
                $account_no = $list['account_no'];
                $firstname = $list['firstname'];
                $lastname = $list['lastname'];

                
                $scholar = ScholarProfile::where('firstname',$firstname)->where('lastname',$lastname)->first();
                if($scholar){
                    $count = Scholar::where('account_no',$account_no)->count();
                    if($count == 0){
                        $isko = Scholar::where('id',$scholar->scholar_id)->update(['account_no' => $account_no, 'is_completed' => 1]);
                        if($isko){
                            array_push($success,$account_no);
                        }else{
                            array_push($failed,$account_no);
                        }
                    }else{
                        array_push($duplicate,$account_no);
                    }
                }else{
                    array_push($failed,$account_no);
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

    public function import($request){
        set_time_limit(0);
        $result = \DB::transaction(function () use ($request){
            $lists = $request->lists;
            $success = array();
            $failed = array();
            $duplicate = array();
            
            foreach($lists as $list){
                $count = Scholar::where('spas_id',$list['spas_id'])->count();
                if($count == 0){
                    $scholar = [
                        'spas_id' => $list['spas_id'],
                        'status_id' => $this->status($list['status']),
                        'program_id' => $this->program($list['program']),
                        'subprogram_id' => $this->program($list['subprogram']),
                        'category_id' => $this->category($list['category']),
                        'awarded_year' => $list['year_awarded'],
                    ];

                    \DB::beginTransaction();
                    $scholar_info = Scholar::create($scholar);

                    if($scholar_info){
                        $education = [
                            'scholar_id' => $scholar_info->id,
                            'school_id' => $this->school($list['school']),
                            'course_id' => $this->course($list['course']),
                            'information' => json_encode(
                                $information = [
                                    'school' => $list['school'],
                                    'course' => $list['course'],
                                ]
                            )
                        ];

                        $education_info = ScholarEducation::insertOrIgnore($education);

                        if($education_info){
                            $profile = [
                                'scholar_id' => $scholar_info->id,
                                'email' => (filter_var($list['email'], FILTER_VALIDATE_EMAIL)) ? $list['email'] : NULL,
                                'firstname' => $list['firstname'],
                                'middlename' => $list['middlename'],
                                'lastname' => $list['lastname'],
                                'suffix' => $list['suffix'],
                                'birthday' => $list['birthday'],
                                'sex' => $list['sex'],
                                'contact_no' => $list['contact_no'],
                            ];

                            $profile_info = ScholarProfile::insertOrIgnore($profile);

                            if($profile_info){
                                $address = $this->address(
                                    $list['region'],
                                    $list['province'],
                                    $list['municipality'],
                                    $list['barangay'],
                                    $list['district'],
                                    $list['zipcode'],
                                    $list['address'],
                                    $scholar_info->id,
                                );

                                $address_info = ScholarAddress::insertOrIgnore($address);

                                if($address_info){
                                    array_push($success,$list['spas_id']);
                                    \DB::commit();
                                }else{
                                    array_push($failed,$list['spas_id']);
                                    \DB::rollback();
                                }
                            }else{
                                array_push($failed,$list['spas_id']);
                                \DB::rollback();
                            }
                        }else{
                            array_push($failed,$list['spas_id']);
                            \DB::rollback();
                        }
                    }else{
                        array_push($failed,$list['spas_id']);
                        \DB::rollback();
                    }
                }else{
                    array_push($duplicate,$list['spas_id']);
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

    public function program($name){
        $program = ListProgram::select('id')->where('name',$name)->first();
        $program_id = ($program) ? $program->id : '';
        return $program_id;
    }

    public function category($name){
        $category = ListDropdown::select('id')->where('name',$name)->where('classification','Category')->first();
        $category_id = ($category) ? $category->id : '';
        return $category_id;
    }

    public function course($name){
        $course = ListCourse::select('id')->where('name',$name)->first();
        $course_id = ($course) ? $course->id : '';
        return $course_id;
    }

    public function school($name){
        $school = SchoolCampus::select('id')->where('oldname',$name)->first();
        if($school){
            $school_id = $school->id; 
        }else{
            $school2 = School::select('id')->where('name',$name)->first();
            $school3 = SchoolCampus::select('id')->where('school_id',$school2->id)->first();
            if($school3){
                $school_id = $school3->id; 
            }else{
                $school_id = '';
            }
        }
        return $school_id;
    }

    public function status($name){
        if($name == 'NEW' || $name == 'ONGOING'){
            return 6;
        }else{
            $status = ListStatus::select('id')->where('name',$name)->first();
            return $status->id;
        }
    }

    public function address($region,$province,$municipality,$barangay,$district,$zipcode,$address,$id){
        switch($region){
            case '1':
                $region_code = '010000000';
            break;
            case '2':
                $region_code = '020000000';
            break;
            case '3':
                $region_code = '030000000';
            break;
            case '4a':
                $region_code = '040000000';
            break;
            case '4b':
                $region_code = '170000000';
            break;
            case '5':
                $region_code = '050000000';
            break;
            case '6':
                $region_code = '060000000';
            break;
            case '7':
                $region_code = '070000000';
            break;
            case '8':
                $region_code = '080000000';
            break;
            case '9':
                $region_code = '090000000';
            break;
            case '10':
                $region_code = '100000000';
            break;
            case '11':
                $region_code = '110000000';
            break;
            case '12':
                $region_code = '120000000';
            break;
            case 'NCR':
                $region_code = '13000000';
            break;
            case 'CAR':
                $region_code = '14000000';
            break;
            case 'ARMM':
                $region_code = '15000000';
            break;  
            case 'BARMM':
                $region_code = '15000000';
            break; 
            case 'CARAGA':
                $region_code = '16000000';
            break; 
        }

        $information = [
            'address' => $address,
            'barangay' => $barangay,
            'municipality' => $municipality,
            'province' => $province,
            'region' => $region,
            'district' => $district,
        ];

        $is_completed = 0;
        $is_within = 1;
        $district = null;
        $barangay = null;
        // ($municipality == 'ZAMBOANGA CITY') ? $province = 'ZAMBOANGA CITY' : $province;

        if($province){
            $data = LocationProvince::with('region')
            ->where(function($query) use ($province) {  
                $query->where('name','LIKE', '%'.$province.'%');
            })->first();
            $province = $data->code;
            $region = $data->region->code;
            if($region_code != $region){
                $is_within = 0;
            }
        }
        if($municipality != null){
            $m = LocationMunicipality::where(function($query) use ($municipality) {  
                $query->where('name','LIKE', '%'.$municipality.'%');
            })
            ->when($province, function ($query, $province) {
                $query->whereHas('province',function ($query) use ($province) {
                    $query->where('province_code',$province);
                });
            })
            ->first();
            
            if($m != null){
                if($zipcode){
                    $m->zipcode = $zipcode;
                    $m->save();
                }
                $municipality = $m->code;
                if(!$m->is_chartered){
                    $district = $m->district;
                }
            }else{
                $municipality = strtolower($municipality);
                $test = strpos($municipality,'city');
                if($test){
                    $list = str_replace(" city",'',$municipality);
                    $municipality = 'City of '.$list;

                    $m = LocationMunicipality::where(function($query) use ($municipality) {  
                        $query->where('name','LIKE', '%'.$municipality.'%');
                    })
                    ->when($province, function ($query, $province) {
                        $query->whereHas('province',function ($query) use ($province) {
                            $query->where('province_code',$province);
                        });
                    })
                    ->first();
    
                    if($m != null){
                        if($zipcode){
                            $m->zipcode = $zipcode;
                            $m->save();
                        }
                        $municipality = $m->code;
                        if(!$m->is_chartered){
                            $district = $m->district;
                        }
                    }else{
                        $municipality = null;
                    }
                }else{
                    $municipality = null;
                }
            }
        }

        if($province != null && $municipality != null && $barangay != null && $district != null){
            $is_completed = 1;
        }   

        $address = [
            'is_permanent' => 1,
            'is_within' => $is_within,
            'address' => $address,
            'barangay_code' => $barangay,
            'municipality_code' => $municipality,
            'province_code' => $province,
            'region_code' => $region,
            'district' => $district,
            'is_completed' => $is_completed,
            'created_at' => now(),
            'updated_at' => now(),
            'information' => json_encode($information),
            'scholar_id' => $id
        ];
        // $a = ProfileAddress::insertOrIgnore($address);
        return $address;
    
    }

    public function api(Request $request){
        $bearer = $request->bearerToken();
        $token = PersonalAccessToken::findToken($bearer);
        $region = $token->tokenable->profile->agency->region_code;
       
        $data = Scholar::with('address')->with('education')->with('profile')
        ->whereHas('education',function ($query) use ($region) {
            $query->whereHas('school',function ($query) use ($region) {
                $query->where('assigned_region',$region); 
            });
        })
        ->get();
        return $data;
    }

    public function sync(Request $request){
        $success = []; $failed = [];
      
        $scholars = $request->scholars;
        $addresses = $request->addresses;
        $educations = $request->educations;
        $profiles = $request->profiles;

        foreach($addresses as $a){
            $spas_id = $a['scholar']['spas_id'];
            $scholar = Scholar::where('spas_id',$spas_id)->first();
            $data = $scholar->address()->update([
                'address' => $a['address'],
                'region_code' => $a['region_code'],
                'province_code' => $a['province_code'],
                'municipality_code' => $a['municipality_code'],
                'barangay_code' => $a['barangay_code'],
                'district' => $a['district'],
                'is_completed' => 1
            ]);
            if($data){
                $container = [
                    'spas_id' => $spas_id,
                    'type' => 'address'
                ];
                array_push($success,$container);
            }else{
                array_push($failed,$spas_id);
            }
        }

        foreach($scholars as $s){
            $spas_id = $s['spas_id'];
            $scholar = Scholar::where('spas_id',$spas_id)->first();
            $data = $scholar->update([
                'account_no' => $s['account_no'],
                'status_id' => $s['status_id'],
            ]);
            if($data){
                $container = [
                    'spas_id' => $spas_id,
                    'type' => 'scholar'
                ];
                array_push($success,$container);
            }else{
                array_push($failed,$spas_id);
            }
        }

        foreach($educations as $e){
            $spas_id = $e['scholar']['spas_id'];
            $scholar = Scholar::where('spas_id',$spas_id)->first();
            $data = $scholar->education()->update([
                'school_id' => $e['school_id'],
                'course_id' => $e['course_id'],
                'level_id' => $e['level_id'],
                'award_id' => $e['award_id'],
                'graduated_year' => $e['graduated_year'],
                'is_completed' => 1
            ]);
            if($data){
                $container = [
                    'spas_id' => $spas_id,
                    'type' => 'education'
                ];
                array_push($success,$container);
            }else{
                array_push($failed,$spas_id);
            }
        }

        foreach($profiles as $p){
            $spas_id = $p['scholar']['spas_id'];
            $scholar = Scholar::where('spas_id',$spas_id)->first();
            $data = $scholar->profile()->update([
                'email' => $p['email'],
                'firstname' => $p['firstname'],
                'lastname' => $p['lastname'],
                'middlename' => $p['middlename'],
                'contact_no' => $s['contact_no'],
                'is_completed' => 1
            ]);
            if($data){
                $container = [
                    'spas_id' => $spas_id,
                    'type' => 'profile'
                ];
                array_push($success,$container);
            }else{
                array_push($failed,$spas_id);
            }
        }

        $array = [
            'success' => $success,
            'failed' => $failed
        ];

        return response()->json($array);
    }
}
