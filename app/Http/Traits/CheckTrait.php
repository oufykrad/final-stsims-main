<?php

namespace App\Http\Traits;

use App\Models\ListCourse;
use App\Models\ListAgency;
use App\Models\ListDropdown;
use App\Models\ListProgram;
use App\Models\SchoolCampus;
use App\Models\ScholarAddress;
use App\Models\ProfileAddress;
use App\Models\LocationBarangay;
use App\Models\LocationMunicipality;
use App\Http\Resources\DefaultResource;

trait CheckTrait {
    public static function level($data) 
    {
        $dropdown_id = ListDropdown::where('classification','Level')->where('name',$data)->pluck('id')->first();
        return $dropdown_id;
    }

    public static function category($data) 
    {
        if($data == 'JLSS - RA 7687'){
            $data = 'RA 7687';
        }else if($data == 'JLSS - MERIT'){
            $data = 'MERIT';
        }else if($data == 'JLSS - RA 10612'){
            $data = 'RA 10612';
        }else{
            $data = $data;
        }

        $dropdown_id = ListProgram::where('name',$data)->pluck('id')->first();
        return $dropdown_id;
    }

    public static function checkCategory($data) 
    {
        if($data == 'JLSS - RA 7687'){
            $is_undergrad = 0;
        }else if($data == 'JLSS - MERIT'){
            $is_undergrad = 0;
        }else if($data == 'JLSS - RA 10612'){
            $is_undergrad = 0;
        }else if($data == 'Jlap'){
            $is_undergrad = 0;
        }else if($data == 'Ra 7687 Sophomore'){
            $is_undergrad = 0;
        }else{
            $is_undergrad = 1;
        }
        return $is_undergrad;
    }

    public function checkCourse($type){
        $data = ListCourse::where('abbreviation',$type)->pluck('id')->first();
        ($data != null) ? $data = $data : $data = NULL;
        return $data;
    }

    public function checkSchool($type){
        ($type == 'MSU - Sindangan Ext.') ? $type = 'MSU-Sindangan' : $type = $type;
        ($type == 'JHCSC') ? $type = 'JHCSC-Mati' : $type = $type;
        $data = SchoolCampus::where('shortcut',$type)->pluck('id')->first();
        ($data != null) ? $data = $data : $data = NULL;
        return $data;
    }

    public function checkAddress($address,$barangay,$id){
        $is_within = 1; $b= null;
        $agency_id = config('app.agency');
        $agency = ListAgency::with('region')->where('id',$agency_id)->pluck('region_code');

        
        $data = LocationMunicipality::with('province.region')
        ->where(function($query) use ($address) {  
            $query->where('name','LIKE', '%'.$address.'%')->orWhere('old_name','LIKE', '%'.$address.'%');
        })
        ->whereHas('province',function ($query) use ($agency) {
            $query->whereHas('region',function ($query) use ($agency) {
                $query->where('region_code',$agency);
            });
        })->first();

        if($data == null){
            $data = LocationMunicipality::with('province.region')
            ->where(function($query) use ($address) {  
                $query->where('name','LIKE', '%'.$address.'%')->orWhere('old_name','LIKE', '%'.$address.'%');
            })->first();

            if($data != null){
                $is_within = 0;
            }

            if($barangay == ''){
                $bar = LocationBarangay::where(function($query) use ($barangay) {  
                    $query->where('name','LIKE', '%'.$barangay.'%')->orWhere('old_name','LIKE', '%'.$barangay.'%');
                })
                ->whereHas('municipality',function ($query) use ($data) {
                    $query->where('code',$data->code);
                })->first();

                if($bar == null){
                    $b = NULL;
                }else{
                    $b = $bar->code;
                }
            }else{
                $b = NULL;
            }
        }

        if($data != null){
            $address = [
                'type' => 'Main',
                'is_within' => $is_within,
                'barangay_code' => $b,
                'municipality_code' => $data->code,
                'province_code' => $data->province->code,
                'region_code' => $data->province->region->code,
                'scholar_id' => $id,
                'created_at' => now(),
                'updated_at' => now()
            ];
            $a = ScholarAddress::insertOrIgnore($address);
        }
    }

    public function searchAddress1($province,$address,$barangay,$id){
        $is_within = 1;
        $agency_id = config('app.agency');
        $agency = ListAgency::with('region')->where('id',$agency_id)->pluck('region_code');


        $bar = LocationBarangay::where(function($query) use ($barangay) {  
            $query->where('name','LIKE', '%'.$barangay.'%')->orWhere('old_name','LIKE', '%'.$barangay.'%');
        })
        ->whereHas('municipality',function ($query) use ($agency) {
            $query->whereHas('province',function ($query) use ($agency) {
                $query->whereHas('region',function ($query) use ($agency) {
                    $query->where('region_code',$agency);
                });
            });
        })->first();

        if($bar == null){
            $b = null;
            $mun = LocationMunicipality::where(function($query) use ($address) {  
                $query->where('name','LIKE', '%'.$address.'%')->orWhere('old_name','LIKE', '%'.$address.'%');
            })->whereHas('province',function ($query) use ($agency) {
                $query->whereHas('region',function ($query) use ($agency) {
                    $query->where('region_code',$agency);
                });
            })->first();

            if($mun == null){
                $m = null;
                $pro = LocationProvince::where(function($query) use ($province) {  
                    $query->where('name','LIKE', '%'.$province.'%')->orWhere('old_name','LIKE', '%'.$province.'%');
                })->whereHas('region',function ($query) use ($agency) {
                    $query->where('region_code',$agency);
                })->first();

                if($pro == null){
                    $p = null;
                    $r = null;
                }else{
                    $m = $pro->code;
                    $r = $pro->region->code;
                }
            }else{
                $m = $mun->code;
                $p = $mun->province->code;
                $r = $mun->province->region->code;
            }
        }else{
            $b = $bar->code;
            $m = $bar->municipality->code;
            $p = $bar->municipality->province->code;
            $r = $bar->municipality->province->region->code;
        }

        $address = [
            'type' => 'Main',
            'is_within' => $is_within,
            'barangay_code' => $b,
            'municipality_code' => $m,
            'province_code' => $p,
            'region_code' => $r,
            'profile_id' => $id,
            'created_at' => now(),
            'updated_at' => now()
        ];
        $a = ProfileAddress::insertOrIgnore($address);

    }

    public function checkAddress2($province,$address,$barangay,$id){
        $is_within = 1; $b= null;
        $agency_id = config('app.agency');
        $agency = ListAgency::with('region')->where('id',$agency_id)->pluck('region_code');

        
        $data = LocationMunicipality::with('province.region')
        ->where(function($query) use ($address) {  
            $query->where('name','LIKE', '%'.$address.'%')->orWhere('old_name','LIKE', '%'.$address.'%');
        })
        ->whereHas('province',function ($query) use ($agency) {
            $query->whereHas('region',function ($query) use ($agency) {
                $query->where('region_code',$agency);
            });
        })->first();

        if($data == null){
            $data = LocationMunicipality::with('province.region')
            ->where(function($query) use ($address) {  
                $query->where('name','LIKE', '%'.$address.'%')->orWhere('old_name','LIKE', '%'.$address.'%');
            })->first();

            if($data != null){
                $is_within = 0;
            }

            if($barangay == ''){
                $bar = LocationBarangay::where(function($query) use ($barangay) {  
                    $query->where('name','LIKE', '%'.$barangay.'%')->orWhere('old_name','LIKE', '%'.$barangay.'%');
                })
                ->whereHas('municipality',function ($query) use ($data) {
                    $query->where('code',$data->code);
                })->first();

                if($bar == null){
                    $b = NULL;
                }else{
                    $b = $bar->code;
                }
            }else{
                $b = NULL;
            }
        }

        if($data != null){
            $address = [
                'type' => 'Main',
                'is_within' => $is_within,
                'barangay_code' => $b,
                'municipality_code' => $data->code,
                'province_code' => $data->province->code,
                'region_code' => $data->province->region->code,
                'profile_id' => $id,
                'created_at' => now(),
                'updated_at' => now()
            ];
            $a = ProfileAddress::insertOrIgnore($address);
        }
    }
}