<?php

namespace App\Http\Resources\Scholar\Sub;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Scholar\ProfileResource;

class SearchResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'account_no' => ($this->account_no == null) ? 'n/a' : $this->account_no,
            'spas_id' => ($this->spas_id == null) ? 'n/a' : $this->spas_id,
            'awarded_year' => $this->awarded_year,
            'status' => $this->status,
            'program' => $this->program,
            'profile' => new ProfileResource($this->profile), 
            'semesters' =>  $this->education->school->semesters,
        ];
    }
}
