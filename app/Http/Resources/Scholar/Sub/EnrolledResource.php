<?php

namespace App\Http\Resources\Scholar\Sub;

use Illuminate\Http\Resources\Json\JsonResource;

class EnrolledResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'academic_year' => $this->semester->academic_year,
            'semester' => $this->semester->semester->name,
            'level' => $this->level->others,
            'school' => ucwords(strtolower($this->semester->school->school->name)),
            'campus' => ucwords(strtolower($this->semester->school->campus)),
            'avatar' => ($this->scholar->profile->user) ? $this->scholar->profile->user : 'avatar.jpg',
            'scholar' => $this->scholar->profile->firstname.' '.$this->scholar->profile->middlename.' '.$this->scholar->profile->lastname,
            'attachment' => json_decode($this->attachment),
            'enrolled_at' => $this->created_at
        ];
    }
}
