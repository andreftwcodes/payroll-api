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
            'loan_no' => 'required|numeric|integer',
            'amount' => 'required|numeric|gt:0',
            'loaned_at' => 'required|date'
        ];
    }
}