<?php

namespace App\Http\Resources;

use Hashids\Hashids;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $hashids = new Hashids('krad',10);
        $code = $hashids->encode($this->id);
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'code' => $code,
            'name' => ucwords(strtolower($this->school->name)),
            'campus' => ucwords(strtolower($this->campus)),
            'shortcut' => $this->school->shortcut,
            'is_main' => $this->is_main,
            'is_active' => $this->is_active,
            'address' => $this->address,
            'grading' => $this->grading,
            'term' => $this->term,
            'avatar' => $this->school->avatar,
            'class' => $this->school->class,
            'region' => $this->municipality->province->region,
            'province' => $this->municipality->province,
            'municipality' => $this->municipality,
            'assigned' => $this->assigned,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'courses' => ($this->courses != null) ? $this->courses : ''
        ];
    }
}
