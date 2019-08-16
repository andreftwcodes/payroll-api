<?php

namespace App\Http\Requests\CashAdvance;

use Illuminate\Foundation\Http\FormRequest;

class CashAdvanceStoreRequest extends FormRequest
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
            'date' => 'required|date',
            'credit' => 'numeric|gt:0|nullable|required_without:debit',
            'debit' => 'numeric|gt:0|nullable|required_without:credit'
        ];
    }
}
