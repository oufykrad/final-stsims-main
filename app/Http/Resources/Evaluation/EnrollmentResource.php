<?php

namespace App\Http\Resources\Evaluation;

use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
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
            'is_clear' => $this->is_clear,
            'is_locked' => $this->is_locked,
            'academic_year' => $this->semester->academic_year,
            'semester' => $this->semester->semester->name,
            'level' => $this->level->others,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'has_failing' => $this->failed(),
            'has_empty' => $this->empty(),
            'attachment' => json_decode($this->attachment,true),
            'lists' => $this->lists,
            'created_at' => $this->created_at,
        ];
    }
}
