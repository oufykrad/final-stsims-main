<?php

namespace App\Http\Resources\School;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchResource extends JsonResource
{
    public function toArray($request)
    {
        $name = ucwords(strtolower($this->school->name));
        $campus = ($this->is_main) ? '' : ' - '.ucwords(strtolower($this->campus)) ;
        return [
            'id' => $this->id,
            'name' => $name.' '.$campus,
            'courses' => CourseResource::collection($this->courses),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
