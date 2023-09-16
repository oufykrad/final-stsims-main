<?php

namespace App\Http\Resources\Scholars;

use Hashids\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexResource extends JsonResource
{
    public function toArray($request)
    {
        $hashids = new Hashids('krad',10);
        $id = $hashids->encode($this->id);
        
        return [
            'code' => $id,
            'id' => $this->id,
            'spas_id' => $this->spas_id,
            'account_no' => $this->account_no,
            'program' => $this->program,
            'subprogram' => $this->subprogram,
            'category' => $this->category,
            'status' => $this->status,
            'awarded_year' => $this->awarded_year,
            'is_completed' => $this->is_completed,
            'user' => ($this->user != null) ? new UserResource($this->user) : null,
            'profile' => new ProfileResource($this->profile), 
            'address' => new AddressResource($this->address),
            'education' =>  new EducationResource($this->education),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
