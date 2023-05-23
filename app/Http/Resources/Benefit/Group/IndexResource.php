<?php

namespace App\Http\Resources\Benefit\Group;

use Illuminate\Http\Resources\Json\JsonResource;

class IndexResource extends JsonResource
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
            'semester' => $this->semester,
            'lists' => ListResource::collection($this->lists)
        ];
    }
}
