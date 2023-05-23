<?php

namespace App\Http\Resources\Benefit\Group;

use Illuminate\Http\Resources\Json\JsonResource;

class ListResource extends JsonResource
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
            'count' => $this->count,
            'is_first' => $this->is_first,
            'is_hold' => $this->is_hold,
            'start_at' => $this->start_at,
            'scholar_id' => $this->scholar->id,
            'spas_id' => $this->scholar->spas_id,
            'avatar' => ($this->scholar->profile->user) ? $this->scholar->profile->user->avatar : 'avatar.jpg',
            'firstname' => $this->scholar->profile->firstname,
            'lastname' => $this->scholar->profile->lastname,
            'middlename' => $this->scholar->profile->middlename,
            'status' => $this->scholar->status
        ];
    }
}
