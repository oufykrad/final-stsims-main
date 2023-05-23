<?php

namespace App\Http\Resources\Scholar;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->scholar->id,
            'name' => $this->lastname.', '.$this->firstname.' '.$this->middlename,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'middlename' => $this->middlename,
            'program' => $this->scholar->program->name,
            'status' => $this->scholar->status,
            'avatar' => ($this->user) ? $this->user->avatar : 'avatar.jpg',
            'school' => ucwords(strtolower($this->scholar->education->school->school->name)),
            'course' => $this->scholar->education->course->name
        ];
    }
}
