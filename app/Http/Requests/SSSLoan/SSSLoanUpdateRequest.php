<?php

namespace App\Http\Requests\SSSLoan;

use Illuminate\Foundation\Http\FormRequest;

class SSSLoanUpdateRequest extends FormRequest
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
            'employee_id' => 'required|numeric|integer',
            'loan_no' => 'required|numeric|integer|unique:sss_loans,loan_no,' . $this->input('id'),
            'amount_loaned' => 'required|numeric|gt:0|regex:/^\d*(\.\d{1,2})?$/',
            'amortization_amount' => 'required|numeric|gt:0|regex:/^\d*(\.\d{1,2})?$/',
            'payment_terms' => 'required|numeric|gt:0',
            'date_loaned' => 'required|date'
        ];
    }

    public function messages()
    {
        return [
            'employee_id.required' => 'The employee field is required.',
            'amount_loaned.regex' => 'The amount loaned must only be two decimal places.',
            'amortization_amount.regex' => 'The amortization amount must only be two decimal places.'
        ];
    }
}
