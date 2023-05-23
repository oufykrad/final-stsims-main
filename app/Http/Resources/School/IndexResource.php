<?php

namespace App\Http\Resources\School;

use Hashids\Hashids;
use App\Http\Resources\DefaultResource;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexResource extends JsonResource
{
    public function toArray($request)
    {
        $hashids = new Hashids('krad',10);
        $code = $hashids->encode($this->id);

        return [
            'id' => $this->id,
            'code' => $code,
            'name' => ucwords(strtolower($this->school->name)),
            'campus' => ucwords(strtolower($this->campus)),
            'address' => ucwords(strtolower($this->address)),
            'municipality' => $this->municipality,
            'class' => $this->school->class,
            'avatar' => $this->school->avatar,
            'term' => $this->term,
            'grading' => $this->grading,
            'status' => ($this->is_active) ? 'active' : 'inactive',
            'courses' => CourseResource::collection($this->courses),
            'semesters' => SemesterResource::collection($this->semesters),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
