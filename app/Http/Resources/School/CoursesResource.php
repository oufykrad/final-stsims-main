<?php

namespace App\Http\Resources\School;

use Illuminate\Http\Resources\Json\JsonResource;

class CoursesResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'years' => $this->years,
            'course' => $this->course,
            'validity' => $this->validity,
            'certification' => $this->certification,
            'is_active' => $this->is_active,
            'prospectuses' => $this->prospectuses
        ];
    }
}
