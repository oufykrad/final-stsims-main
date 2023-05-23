<?php

namespace App\Http\Resources\Scholar;


use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray($request)
    {
        $details = $this->info['info'];
        $barangay = ($this->barangay == null) ? (isset($details->barangay)) ? $details->barangay : '-' : $this->barangay->name;
        $municipality = ($this->municipality == null) ? $details->municipality : $this->municipality->name;
        $province = ($this->province == null) ? $details->province : $this->province->name;

        $name = ucwords(strtolower($barangay.', '.$municipality.', '.$province));

        return [
            'id' => $this->id,
            'name' => $name,
            'region' => $this->region,
            'province' => $this->province,
            'municipality' => $this->municipality,
            'barangay' => $this->barangay,
            'has_region' => ($this->region_code == null) ? false : true,
            'has_province' => ($this->province_code == null) ? false : true,
            'has_municipality' => ($this->municipality_code == null) ? false : true,
            'has_barangay' => ($this->barangay_code == null) ? false : true,
            'is_completed' => $this->is_completed
        ];
    }
}
