<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeExtrasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'rate' => 'required|numeric|min:1|regex:/^\d*(\.\d{1,2})?$/',
            'locale' => 'required|numeric',
            'payment_period' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'rate.regex' => 'The rate must only be two decimal places.'
        ];
    }
    
}
