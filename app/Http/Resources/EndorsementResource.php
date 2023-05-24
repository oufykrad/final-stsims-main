<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EndorsementResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'endorsed_region' => $this->region_by->region,
            'endorsed_name' => $this->region_by->name,
            'is_seened' => $this->is_seened,
            'name' => $this->scholar->name,
            'spas_id' => $this->scholar->spas_id,
            'information' => json_decode($this->scholar->information,true),
            'created_at' => $this->created_at
        ];
    }
}
