<?php

namespace App\Http\Controllers;

use App\Models\Qualifier;
use App\Models\QualifierProfile;
use App\Models\QualifierAddress;
use App\Models\Scholar;
use App\Models\ScholarEducation;
use App\Models\ScholarAddress;
use App\Models\ScholarProfile;
use App\Models\ListStatus;
use App\Models\ListProgram;
use App\Models\ListDropdown;
use App\Models\LocationProvince;
use App\Models\LocationBarangay;
use App\Models\LocationMunicipality;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\QualifiersImport;
use Illuminate\Http\Request;
use App\Http\Resources\Qualifiers\IndexResource;

class QualifierController extends Controller
{
    public function index(Request $request){
        if($request->lists){
            $info = (!empty(json_decode($request->info))) ? json_decode($request->info) : NULL;
            $location = (!empty(json_decode($request->location))) ? json_decode($request->location) : NULL;
            $keyword = $info->keyword;

            $data = IndexResource::collection(
                Qualifier::
                with('address.region','address.province','address.municipality','address.barangay')
                ->with('profile','program','subprogram','status','type')
                ->whereHas('profile',function ($query) use ($keyword) {
                    $query->when($keyword, function ($query, $keyword) {
                        $query->where(\DB::raw('concat(firstname," ",lastname)'), 'LIKE', '%'.$keyword.'%')
                        ->where(\DB::raw('concat(lastname," ",firstname)'), 'LIKE', '%'.$keyword.'%');
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
                ->where(function ($query) use ($info,$keyword) {
                    ($info->program == null) ? '' : $query->where('program_id',$info->program);
                    ($info->subprogram == null) ? '' : $query->where('subprogram_id',$info->subprogram);
                    ($info->status == null) ? '' : $query->where('status_id',$info->status);
                    ($info->year == null) ? '' : $query->where('qualifier_year',$info->year);
                    // ($keyword == null) ? '' : $query->where('spas_id', 'LIKE', '%'.$keyword.'%');
                 })
                ->paginate($request->counts)
                ->withQueryString()
            );
            return $data;
        }else{
            return inertia('Modules/Qualifiers/Index');
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
            case 'update':
                return $this->update($request);
            break;
            case 'enroll':
                return $this->enroll($request);
            break;
        }
    }

    public function enroll($request){ 
        // dd($request->user);
        $scholar = $request->user;

        $count = Scholar::where('spas_id',$scholar['spas_id'])->count();
        if($count == 0){
            $info = [
                'spas_id' => $scholar['spas_id'],
                'account_no' => $request->account_no,
                'program_id' => $scholar['program']['id'],
                'subprogram_id' => $scholar['subprogram']['id'],
                'category_id' => 21,
                'status_id' => 7,
                'awarded_year' =>  $scholar['qualified_year'],
                'is_completed' => 1,
            ];

            \DB::beginTransaction();
            $scholar_info = Scholar::create($info);

            if($scholar_info){
                $education = [
                    'school_id' => $request->school_id,
                    'course_id' => $request->course_id,
                    'level_id' => 24, //temp fixed value / 1st year
                    'information' => json_encode($info = []),
                    'is_completed' => 1,
                    'is_enrolled' => 1,
                    'is_shiftee' => 0,
                    'is_transferee' => 0,
                    'scholar_id' => $scholar_info->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $education_info = ScholarEducation::insertOrIgnore($education);

                if($education_info){
                    $profile = [
                        'email' => $scholar['profile']['email'],
                        'firstname' => $scholar['profile']['firstname'],
                        'middlename' => $scholar['profile']['middlename'],
                        'lastname' => $scholar['profile']['lastname'],
                        'suffix' => $scholar['profile']['suffix'],
                        'sex' => $scholar['profile']['sex'],
                        'contact_no' => $scholar['profile']['contact_no'],
                        'sex' => $scholar['profile']['sex'],
                        'birthday' => $scholar['profile']['birthday'],
                        'is_completed' => 1,
                        'scholar_id' => $scholar_info->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    $profile_info = ScholarProfile::insertOrIgnore($profile);

                    if($profile_info){
                        $address = [
                            'address' => 'n/a',
                            'region_code' => $scholar['address']['region']['code'],
                            'province_code' => $scholar['address']['province']['code'],
                            'municipality_code' => $scholar['address']['municipality']['code'],
                            'barangay_code' => $scholar['address']['barangay']['code'],
                            'district' => $scholar['address']['district'],
                            'is_permanent' => 1,
                            'is_within' => 1,
                            'is_completed' => 1,
                            'scholar_id' => $scholar_info->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];

                        $address_info = ScholarAddress::insertOrIgnore($address);

                        if($address_info){
                            $qualifier = Qualifier::where('id',$scholar['id'])->update(['status_type' => 17, 'status_id' => 18,'is_completed' => 1]);
                            $type = 'bxs-check-circle';
                            $message = 'Qualifier tag as scholar successfully. Thanks';
                            \DB::commit();
                        }else{
                            $type = 'bxs-x-circle';
                            $message = 'Contact Administrator';
                            \DB::rollback();
                        }
                    }else{
                        $type = 'bxs-x-circle';
                        $message = 'Contact Administrator';
                        \DB::rollback();
                    }
                }else{
                    $type = 'bxs-x-circle';
                    $message = 'Contact Administrator';
                    \DB::rollback();
                }
            }else{
                $type = 'bxs-x-circle';
                $message = 'Contact Administrator';
                \DB::rollback();
            }
        }else{
            $type = 'bxs-x-circle';
            $message = 'Duplicate Entry';
        }

        return back()->with([
            'message' => $message,
            'data' =>  '',
            'type' => $type
        ]); 

    }

    public function update($request){
        $data = QualifierAddress::where('id',$request->address_id)->update($request->except('id','address_id','editable','type'));
        $q =QualifierAddress::find($request->address_id);
        $qualifier = Qualifier::
            with('address.region','address.province','address.municipality','address.barangay')
            ->with('profile','program','subprogram','status')
            ->where('id',$q->qualifier_id)
            ->first();

        return back()->with([
            'message' => 'Scholar updated successfully. Thanks',
            'data' =>  new IndexResource($qualifier),
            'type' => 'bxs-check-circle'
        ]); 
    }

    public function preview($request){
        $data =  Excel::toCollection(new QualifiersImport,$request->import_file);
        $rows = $data[0]; 

        foreach($rows as $row){ 
            if($row[1] != ''){

                $count = substr_count($row[5],'*');

                $birthday = ($row[8] - 25569) * 86400;
                $birthday = 25569 + ($birthday / 86400);
                $birthday = ($birthday - 25569) * 86400;
                $birthday = gmdate("Y-m-d", $birthday);

                if($count == 2){
                    $status = 20;
                }else if($count == 1){
                    $status = 19;
                }else{
                    $status = 18;
                }

                $middlename = str_replace("*","",$row[5]);

                $information[] = [
                    'spas_id' => $row[0],
                    'firstname' => strtoupper(strtolower($row[4])),
                    'middlename' => strtoupper(strtolower($middlename)),
                    'lastname' => strtoupper(strtolower($row[3])),
                    'suffix' => strtoupper(strtolower($row[6])),
                    'sex' => $row[7],
                    'birthday' => $birthday,
                    'barangay' => strtoupper(strtolower($row[14])),
                    'municipality' => strtoupper(strtolower($row[15])),
                    'province' => strtoupper(strtolower($row[16])),
                    'region' => strtoupper(strtolower($row[19])),
                    'district' => strtoupper(strtolower($row[18])),
                    'zipcode' => strtoupper(strtolower($row[17])),
                    'hs_school' => strtoupper(strtolower($row[20])),
                    'email' => strtolower($row[9]),
                    'contact_no' => strtoupper(strtolower($row[10])),
                    'year_qualified' => 2023,
                    'program' => 'RA 7687',
                    'status' => $status,
                    'subprogram' => strtoupper(strtolower($row[2]))
                ];
            }
        }
        return $information;
    }

    public function import($request){
        set_time_limit(0);
        $result = \DB::transaction(function () use ($request){
            $lists = $request->lists;
            $success = array();
            $failed = array();
            $duplicate = array();
            
            foreach($lists as $list){
                $count = Qualifier::where('spas_id',$list['spas_id'])->count();
                if($count == 0){
                    $qualifier = [
                        'spas_id' => $list['spas_id'],
                        'status_id' => $list['status'],
                        'status_type' => 14,
                        'program_id' => $this->program($list['program']),
                        'subprogram_id' => $this->program($list['subprogram']),
                        'qualified_year' => $list['year_qualified'],
                    ];

                    \DB::beginTransaction();
                    $qualifier_info = Qualifier::create($qualifier);
                    if($qualifier_info){
                        $profile = [
                            'qualifier_id' => $qualifier_info->id,
                            'email' => (filter_var($list['email'], FILTER_VALIDATE_EMAIL)) ? $list['email'] : NULL,
                            'firstname' => $list['firstname'],
                            'middlename' => $list['middlename'],
                            'lastname' => $list['lastname'],
                            'suffix' => $list['suffix'],
                            'birthday' => $list['birthday'],
                            'sex' => $list['sex'],
                            'contact_no' => $list['contact_no'],
                        ];
                        $profile_info = QualifierProfile::insertOrIgnore($profile);

                        if($profile_info){
                            $address = $this->address(
                                $list['region'],
                                $list['province'],
                                $list['municipality'],
                                $list['barangay'],
                                $list['district'],
                                $list['zipcode'],
                                $list['hs_school'],
                                $qualifier_info->id,
                            );
                            
                            $address_info = QualifierAddress::insertOrIgnore($address);
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

    public function address($region,$province,$municipality,$barangay,$district,$zipcode,$hs_school,$id){
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
            'hs_school' => $hs_school,
            'barangay' => $barangay,
            'municipality' => $municipality,
            'province' => $province,
            'region' => $region,
            'district' => $district,
            'zipcode' => $zipcode,
        ];

        $is_completed = 0;

        if($province){
            $data = LocationProvince::with('region')
            ->where(function($query) use ($province) {  
                $query->where('name','LIKE', '%'.$province.'%');
            })->first();
            $province = $data->code;
            $region = $data->region->code;
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


        if($barangay != null){
            $barangay = str_replace("STO.","Santo",$barangay);
            $barangay = str_replace("STA.","Santa",$barangay);

            $b = LocationBarangay::where(function($query) use ($barangay) {  
                $query->where('name','LIKE', '%'.$barangay.'%');
            })
            ->when($municipality, function ($query, $municipality) {
                $query->whereHas('municipality',function ($query) use ($municipality) {
                    $query->where('municipality_code',$municipality);
                });
            })
            ->first();
            if($b != null){
                $barangay = $b->code;
            }else{
                $barangay = null;
            }
        }

        if($province != null && $municipality != null && $barangay != null && $district != null){
            $is_completed = 1;
        }   

        $address = [
            'hs_school' => $hs_school,
            'barangay_code' => $barangay,
            'municipality_code' => $municipality,
            'province_code' => $province,
            'region_code' => $region,
            'district' => $district,
            'zipcode' => $zipcode,
            'is_completed' => $is_completed,
            'created_at' => now(),
            'updated_at' => now(),
            'information' => json_encode($information),
            'qualifier_id' => $id
        ];
        return $address;
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

    public function status($name){
        if($name == 'NEW' || $name == 'ONGOING'){
            return 6;
        }else{
            $status = ListStatus::select('id')->where('name',$name)->first();
            return $status->id;
        }
    }
}

