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

trait ScholarTrait { //Storing Scholar
    
    public static function single($request){
        $parents = [
            'mother' => $request->mother,
            'father' => $request->father,
        ];

        $information2 = [
            'birth_place' => 'n/a',
            'course' => 'n/a',
            'school' => 'n/a',
            'address' => 'n/a',
            'parents' => $parents
        ];
        ($request->information) ? $information = json_decode($request->information,true) : $information = $information2;
        $info = [];

        $data = Profile::create(array_merge($request->all(),['information' => json_encode($information)]));
        $data->address()->create(array_merge($request->all(), ['type' => 'original','is_completed' => 1]));
        $test = $data->scholar()->create(array_merge($request->all(), ['awarded_year' => '2022']));
        $test->education()->create(array_merge($request->all(),['information' => json_encode($info)]));
        $data = Scholar::findOrFail($test->id);

        if($data){
            $link = config('app.api_link');
            if($request->type == 'endorsed'){
                try{
                    $url = $link.'/api/endorsement/update';
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array(
                            'code' => '01101011 01110010 01100001 01100100',
                            'id' => $request->id
                        ),
                    ));
                    $response = curl_exec($curl);
                    curl_close($curl);
                    $data = json_decode($response);
                    return $data;
                    
                } catch (Exception $e) {
                    echo 'Caught exception: ',  $e->getMessage(), "\n";
                }
            }
        }

        return new IndexResource($data);
    }

    public static function qualifier($request){
        $info = []; $is_completed = 1;

        (\Request::has('school_id')) ? '' : $is_completed = 0;
        (\Request::has('course_id')) ? '' : $is_completed = 0;
        // (\Request::has('subcourse_id')) ? '' : $is_completed = 0;
        (\Request::has('level_id')) ? '' : $is_completed = 0;

        $data = Scholar::create($request->all());
        // $data->address()->create(array_merge($request->all(), ['type' => 'original']));
        $data->education()->create(['information' => json_encode($info), 'is_completed' => $is_completed]);
        if($data){
            $user = User::create(array_merge($request->all(), ['password' => bcrypt('dost9ict'),'role' => 'Scholar']));
            $data->profile()->update(['user_id'=> $user->id]);
            $qualifier = Qualifier::where('id',$request->id)->update(['is_qualified' => 1]);

            return new IndexResource($data);
        }
    }

    public static function true($request){
        $data = Scholar::with('profile')->with('education.school')->where('id',$request->id)->first();
        if($request->type == 'old'){
            $request['is_completed'] = 1;
            $data->education()->update($request->except('id','editable','type'));
            $data->update($request->except('editable','type'));
            if($request['school_id'] != '' && $request['course_id'] != '' && $request['level_id'] != ''){
                $request['status_id'] = 31;
                $data->update(array_merge($request->except('editable')));
                $wew = ScholarStatus::create([
                    'scholar_id' => $request->id,
                    'status_id' => $request->status_id,
                    'updated_by' => \Auth::user()->id,
                ]);
            }
            $data = Profile::with('address.region','address.province','address.municipality','address.barangay','user')
            ->with('scholar.program')
            ->with('scholar.education.school.school','scholar.education.course')
            ->where('id',$request->id)->first();
            // $data = Scholar::with('profile')->with('profile.address.municipality.province.region')->with('education.school.school','education.course')->where('id',$request->id)->first();
        }else{
            $request['status_id'] = 31;
            $data->update(array_merge($request->except('editable')));
            $wew = ScholarStatus::create([
                'scholar_id' => $request->id,
                'status_id' => $request->status_id,
                'updated_by' => \Auth::user()->id,
            ]);
        }
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

    public static function login($request){
        $user = new User;
        $user->password  = bcrypt('dost9ict');
        $user->role = 'Scholar';
        $user->email = $request->email;
    
        if($user->save()){
            $profile = Profile::where('id',$request->id)->first();
            $profile->user_id = $user->id;
            $profile->email = $request->email;
            $profile->save();
        }

        $data =  Scholar::with('profile.address.region','profile.address.province','profile.address.municipality','profile.address.barangay','profile.user')
        ->with('program')->with('profile.user')->with('education.school.school','education.course')->where('profile_id',$request->id)->first();
        return new IndexResource($data);
    }

    public function api($request){
        set_time_limit(0);
        $result = \DB::transaction(function () use ($request){
            $scholars = $request->users;
            $users = array();
            $success = array();
            $failed = array();
            $duplicate = array();
        

            foreach($scholars as $scholar){
                
                $count = Scholar::where('spas_id',$scholar['spas_id'])->count();
                if($count == 0){

                    $parents = [
                        'mother' => 'n/a',
                        'father' => 'n/a',
                    ];

                    $information = [
                        'birth_place' => 'n/a',
                        'course' => 'n/a',
                        'school' => 'n/a',
                        'address' => 'n/a',
                        'level' => 'n/a',
                        'parents' => $parents
                    ];

                    $user = [ 
                        'is_completed' => 1,
                        'email' => $scholar['profile']['email'],
                        'firstname' => $scholar['profile']['firstname'],
                        'middlename' => $scholar['profile']['middlename'],
                        'lastname' => $scholar['profile']['lastname'],
                        'suffix' => $scholar['profile']['suffix'],
                        'gender' => $scholar['profile']['gender'],
                        'mobile' => $scholar['profile']['mobile'],
                        'birthday' => $scholar['profile']['birthday'],
                        'information' => json_encode($information),
                        'created_at'	=> now(),
                        'updated_at'	=> now()
                    ];

                    \DB::beginTransaction();
                    $profile = Profile::create($user);

                    if($profile){            
                        $haha = [
                            'spas_id' => $scholar['spas_id'],
                            'lrn' => $scholar['lrn'],
                            'is_completed' => 1,
                            'is_undergrad' => $scholar['is_undergrad'],
                            'program_id' => $scholar['program']['id'],
                            'status_id' =>  $scholar['status']['id'],
                            'profile_id' => $profile->id,
                            'old_id' =>  $scholar['id'],
                            'awarded_year' =>  $scholar['awarded_year'],
                            'created_at'	=> now(),
                            'updated_at'	=> now()
                        ];
                        
                        $q = Scholar::create($haha); 
                        if($q){
                            $is_completed = 1;

                            $school = [
                                'scholar_id' => $q->id,
                                'course_id'=> $scholar['education']['course']['id'],
                                'school_id'=> $scholar['education']['school']['id'],
                                'level_id' => $scholar['education']['level']['id'],
                                'created_at'	=> now(),
                                'updated_at'	=> now(),
                                'is_completed' => $is_completed
                            ];
                            $s = ScholarEducation::insertOrIgnore($school);

                            $address = [
                                'type' => 'Main',
                                'profile_id' => $profile->id,
                                'region_code'=> $scholar['address']['region_code'],
                                'province_code'=> $scholar['address']['province_code'],
                                'municipality_code'=> $scholar['address']['municipality_code'],
                                'barangay_code'=> $scholar['address']['barangay_code'],
                                'created_at'	=> now(),
                                'updated_at'	=> now(),
                                'is_completed' => 0
                            ];
                            $s = ProfileAddress::insertOrIgnore($address);
                            // $address = $this->checkAddress($scholar['municipality'],$scholar['barangay'],$q->id);

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

}