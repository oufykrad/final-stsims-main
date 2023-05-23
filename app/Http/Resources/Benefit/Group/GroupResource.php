<?php

namespace App\Http\Resources\Benefit\Group;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'academic_year' => $this->academic_year,
            'is_regular' => $this->is_regular,
            'semester_id' => $this->semester_id,
            'months' => $this->months,
            'lists' => $this->lists,
            'completed' => $this->completed(),
            'hold' => $this->hold(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
