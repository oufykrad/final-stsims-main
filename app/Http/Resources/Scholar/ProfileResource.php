<?php

namespace App\Http\Resources\Scholar;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray($request)
    {
        $info = json_decode($this->information);

        return [
            'id' => $this->id,
            'email' => $this->email,
            'avatar' => ($this->user) ? $this->user->avatar : 'avatar.jpg',
            'gender' => $this->gender,
            'name' =>  ucwords(strtolower($this->firstname)).' '.ucwords(strtolower($this->middlename)).' '.ucwords(strtolower( $this->lastname)),
            'firstname' => ucwords(strtolower($this->firstname)),
            'middlename' => ucwords(strtolower($this->middlename)),
            'lastname' =>ucwords(strtolower( $this->lastname)),
            'birthday' => ($this->birthday == null) ? '' : $this->birthday,
            'suffix' => ($this->suffix == null) ? '' : $this->suffix,
            'gender' => ($this->gender == null) ? '' : $this->gender,
            'mobile' => ($this->mobile == null) ? 'n/a' : $this->mobile,
            'mother' => $info->parents->mother,
            'father' => $info->parents->father
        ];
    }
}
