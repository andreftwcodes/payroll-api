<?php

namespace App\Http\Requests\PaySlip;

use Illuminate\Foundation\Http\FormRequest;

class PaySlipRequest extends FormRequest
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
            'amount_deductible' => 'required|numeric|gt:0|lte:'.$this->input('balance'),
        ];
    }
}
