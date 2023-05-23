<?php

namespace App\Http\Resources\Scholars;

use Illuminate\Http\Resources\Json\JsonResource;

class SchoolResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => ucwords(strtolower($this->school->name)),
            'class' => $this->school->class->name,
            'avatar' => $this->school->avatar,
            'shortcut' => $this->shortcut,
            'is_main' => $this->is_main,
            'campus' => ($this->is_main) ?  '' : ucwords(strtolower($this->campus)),
            'address' => ucwords($this->address)
        ];
    }
}
