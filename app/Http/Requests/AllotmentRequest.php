<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AllotmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
         return [
            'check_no' => 'sometimes|required',
            'checked_at' => 'sometimes|required',
            'credited_at' => 'sometimes|required',
            'remarks' => 'sometimes|required',
        ];
    }
}
