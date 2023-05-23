<?php

namespace App\Http\Resources\Benefit;

use Illuminate\Http\Resources\Json\JsonResource;

class LbpResource extends JsonResource
{
    public function toArray($request)
    {   
        $account_no = str_replace('-', '', $this->account_no);
        $name = strtoupper($this->profile->lastname.', '. $this->profile->firstname);
        $account_no_3 = substr($account_no, 0, 3);
        $total = str_replace('.', '', number_format((float)$this->benefits->sum('amount'), 2, '.', ''));
        $amount = str_pad(($total), 15, '0', STR_PAD_LEFT); 
        $batch = '00001';
        $last = $amount.$account_no_3.$batch;
        $space = str_repeat(" ",50-strlen($account_no.$name));
        $count = strlen($space);
        return [
            'test' => $account_no.$name.$space.$last.'       ',
            'count' => $count.'   '.'wew',
            // 'program' => $this->program->name,
            // 'avatar' => ($this->profile->user) ? $this->profile->user->avatar : 'avatar.jpg',
            // 'benefits' => $this->benefits,
            // 'total' => str_replace('.', '', number_format((float)$this->benefits->sum('amount'), 2, '.', ''))
        ];
    }
}
