<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
       if($this->option == 'semester'){
            return [
                'academic_year' => 'required',
                'start_at' => 'required',
                'end_at' => 'required',
                'semester_id' => 'required|unique:school_semesters,semester_id,NULL,'.$this->semester_id.',academic_year,'.$this->academic_year,
                'school_id' => 'required'
            ];
        }else if($this->option == 'prospectus'){
            return [
                'school_year' => 'sometimes|required'
            ];
        }else{
            return [
                'course_id' => 'required',
                'type' => 'nullable|string|max:100|unique:school_courses,type,NULL,'.$this->id.',course_id,'.$this->course_id, //,school_id,'.$this->school_id,'
                'years' => 'required'
            ];
       }
    }

    public function messages()
    {
        if($this->option == 'semester'){
            $message = [
                'academic_year.required' => 'required',
                'start_at.required' => 'required',
                'end_at.required' => 'required',
                'semester_id.required' => 'required',
            ];
        }else if($this->option == 'prospectus'){
            $message = [
                'school_year.required' => 'required'
            ];
        }else{
            $message = [
                'course_id.required' => 'required',
                'years.required' => 'required'
            ];
        }
        return $message;
    }
}
