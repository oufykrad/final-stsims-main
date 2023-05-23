<?php

namespace App\Http\Resources\Scholars;

use Illuminate\Http\Resources\Json\JsonResource;

class EducationResource extends JsonResource
{
    public function toArray($request)
    {
        $info = json_decode($this->information);

        return [
            'is_completed' => $this->is_completed,
            'school' => ($this->school == null) ? $info->school : new SchoolResource($this->school),
            'course' => ($this->course == null) ? $info->course : $this->course,
            'level' => ($this->level == null) ? 'n/a' : $this->level,
            'award' => ($this->award == null) ? 'n/a' : $this->award,
            'has_school' => ($this->school == null) ? false : true,
            'has_level' => ($this->level == null) ? false : true,
            'has_course' => ($this->course == null) ? false : true,
            'has_subcourse' => ($this->subcourse == null) ? false : true,
        ];
    }
}
