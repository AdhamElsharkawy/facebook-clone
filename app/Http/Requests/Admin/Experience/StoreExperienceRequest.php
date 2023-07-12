<?php

namespace App\Http\Requests\Admin\Experience;

use Illuminate\Foundation\Http\FormRequest;

class StoreExperienceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'company_id' => 'required|exists:companies,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'description' => 'required|string',
            'title' => 'required|string',
            'type' => 'required|integer',
            'is_current' => 'required|boolean'
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'type.integer' => 'Type is not valid ( type must be 1 => Full Time, 2 => Part Time, 3 => Internship)',
        ];

    }
}
