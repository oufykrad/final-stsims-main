<?php

namespace App\Http\Resources\School;

use Hashids\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray($request)
    {
        $hashids = new Hashids('krad',10);
        $id = $hashids->encode($this->id);
        
        return [
            'id' => $this->id,
            'code' => $id,
            'years' => $this->years,
            'type' => $this->type,
            'course' => $this->course->name,
            'prospectuses' => $this->prospectuses
        ];
    }
}
