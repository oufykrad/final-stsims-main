<?php

namespace App\Http\Resources\Scholar;

use Hashids\Hashids;
use App\Models\ListDropdown;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Scholar\Sub\EnrollmentResource;

class IndexResource extends JsonResource
{
    public function toArray($request)
    {
        $info =  json_decode($this->information);
        $hashids = new Hashids('krad',10);
        $id = $hashids->encode($this->id);
        $this->scholar->education->courseInfo = ['name' => $info->course];
        $this->scholar->education->schoolInfo = ['name' => $info->school];
        $this->address->info = ['info' => $info->address];
        $this->scholar->education->scholar_id = $this->scholar->id;

        if($this->scholar->education->school_id){
            $class = $this->scholar->education->school->term->name;
            $types = ListDropdown::where('classification',$class)->get();
        }else{
            $types = [];
        }

        return [
            'id' => $this->scholar->id,
            'code' => $id,
            'account_no' => ($this->scholar->account_no == null) ? 'n/a' : $this->scholar->account_no,
            'lrn' => ($this->scholar->lrn == null) ? 'n/a' : $this->scholar->lrn,
            'spas_id' => ($this->scholar->spas_id == null) ? 'SPAS-'.str_pad(mt_rand(1,99999999),6,'0',STR_PAD_LEFT) : $this->scholar->spas_id,
            'awarded_year' => $this->scholar->awarded_year,
            'program' => $this->scholar->program,
            'benefits' => $this->scholar->benefits,
            'enrollments' => EnrollmentResource::collection($this->scholar->enrollments),
            'status' => $this->scholar->status,
            'is_completed' => $this->scholar->is_completed,
            'is_undergrad' => $this->scholar->is_undergrad,
            'profile' => new ProfileResource($this), 
            'user' => ($this->user != null) ? new UserResource($this->user) : null,
            'address' => new AddressResource($this->address),
            'education' =>  new EducationResource($this->scholar->education),
            'ays' =>    ($this->scholar->education->school_id) ? $this->scholar->education->school->semesters : '',
            'types' => ($this->scholar->education->school_id) ? $types : '',
            'created_at' => $this->scholar->created_at,
            'updated_at' => $this->scholar->updated_at,
            'info' => $info
        ];
    }
}
