<?php

namespace App\Http\Requests\Loans;

use Illuminate\Foundation\Http\FormRequest;

class GovernmentLoanStoreRequest extends FormRequest
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
            'employee_id'   => 'required|numeric|integer',
            'ref_no'        => 'required|numeric|integer|unique:government_loans',
            'subject'       => 'required|string',
            'amount_loaned' => 'required|numeric|gt:0|regex:/^\d*(\.\d{1,2})?$/',
            'amortization'  => 'required|numeric|gt:0|regex:/^\d*(\.\d{1,2})?$/',
            'loaned_at'     => 'required|date'
        ];
    }

    public function messages()
    {
        return [
            'employee_id.required' => 'The employee field is required.',
            'amount_loaned.regex'  => 'The amount loaned must only be two decimal places.',
            'amortization.regex'   => 'The amortization must only be two decimal places.'
        ];
    }
}
