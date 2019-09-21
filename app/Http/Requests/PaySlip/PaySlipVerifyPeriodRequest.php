<?php

namespace App\Http\Requests\PaySlip;

use Illuminate\Foundation\Http\FormRequest;

class PaySlipVerifyPeriodRequest extends FormRequest
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
            'employee_id' => 'required',
            'from' => 'required|before_or_equal:to',
            'to' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'employee_id.required' => 'The employee field is required.'
        ];
    }
}
