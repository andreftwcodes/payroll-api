<?php

namespace App\Http\Requests\Contributions;

use Illuminate\Foundation\Http\FormRequest;

class HeaderContributionRequest extends FormRequest
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
            'title' => 'required',
            'used_at' => 'required|unique:hdr_contributions,used_at,flag'
        ];
    }

    public function messages()
    {
        return [
            'used_at.required' => 'The date use field is required.',
            'used_at.unique' => 'The date use has already been taken.'
        ];
    }
}
