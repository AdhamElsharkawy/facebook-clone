<?php

namespace App\Http\Requests\Admin\Education;

use Illuminate\Foundation\Http\FormRequest;

class StoreEducationRequest extends FormRequest
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
            'degree' => 'required|string',
            'major' => 'required|string',
            'start_date' => 'required',
            'end_date' => 'required',
            'is_current' => 'required|boolean',
            'location' => 'required|string',
            'college_id' => 'required|exists:colleges,id',
            'user_id' => 'required|exists:users,id',
        ];
    }
}
