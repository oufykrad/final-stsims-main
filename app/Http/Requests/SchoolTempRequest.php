<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolTempRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|max:200|unique:school_temps,name,'.$this->id,
            'term_id' => 'sometimes|required',
            'grading_id' => 'sometimes|required',
            'class_id' => 'sometimes|required',
            'shortcut' => 'sometimes|string|required',
            'campus' => 'string|required',
            'is_main' => 'required',
            'address' => 'string|required',
            'region_code' => 'required',
            'province_code' => 'required',
            'municipality_code' => 'required',
        ];
    }

    public function messages()
    {
        $message = [
            'region_code.required' => 'Region required.',
            'province_code.required' => 'Province required.',
            'municipality_code.required' => 'Municipality required.',
        ];
        return $message;
    }
}
