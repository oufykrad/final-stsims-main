<?php

namespace App\Http\Resources\Scholar;

use Hashids\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Scholar\Sub\EnrollmentResource;

class EvaluationResource extends JsonResource
{
    public function toArray($request)
    {
        $info =  json_decode($this->profile->information);
        $hashids = new Hashids('krad',10);
        $id = $hashids->encode($this->id);

        $this->education->addressInfo = ['name' => (is_array($info->address)) ? $info->address->name2 : $info->address, 'is_migrated' => 0];
        $this->education->courseInfo = ['name' => $info->course];
        $this->education->scholar_id = $this->id;
        
        return [
            'id' => $this->id,
            'code' => $id,
            'account_no' => ($this->account_no == null) ? 'n/a' : $this->account_no,
            'spas_id' => ($this->spas_id == null) ? 'n/a' : $this->spas_id,
            'awarded_year' => $this->awarded_year,
            'status' => $this->status,
            'program' => $this->program,
            'profile' => new ProfileResource($this->profile), 
            'education' =>  new EducationResource($this->education),
            'enrollments' => EnrollmentResource::collection($this->enrollments)
        ];
    }
}
