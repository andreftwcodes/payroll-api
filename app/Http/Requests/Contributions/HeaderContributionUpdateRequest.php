<?php

namespace App\Http\Requests\Contributions;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class HeaderContributionUpdateRequest extends FormRequest
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
            'used_at' => [
                'required',
                Rule::exists('hdr_contributions')->where(function ($query) {
                    $query->where('id', $this->input('id'));
                    $query->where('flag', $this->input('flag'));
                }),
            ]
        ];
    }

    public function messages()
    {
        return [
            'used_at.required' => 'The date use field is required.',
            'used_at.exists' => 'The date use has already been taken.',
        ];
    }

}
