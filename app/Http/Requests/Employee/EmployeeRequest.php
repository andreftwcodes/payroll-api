<?php

namespace App\Http\Requests\Employee;

use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
            'firstname' => 'required|string',
            'middlename' => 'nullable|string',
            'lastname'  => 'required|string',
            'gender'    => 'required',
            'contact'   => 'nullable|numeric',
            'birthdate' => 'date|before_or_equal:' . Carbon::now()->toDateString()
        ];
    }
}
