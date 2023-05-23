<?php

namespace App\Http\Resources\Benefit;

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
            'account' => rand ( 10000 , 99999 ) .'-'.rand ( 10000 , 99999 ).'-'.rand ( 10000 , 99999 ),
            'account_no' => ($this->account_no == null) ? 'n/a' : $this->account_no,
            'name' => $this->profile->lastname.', '. $this->profile->firstname.' '. $this->profile->middlename,
            'program' => $this->program->name,
            'avatar' => ($this->profile->user) ? $this->profile->user->avatar : 'avatar.jpg',
            'benefits' => $this->benefits,
            'total' => $this->benefits->sum('amount')
        ];
    }
}
