<?php

namespace App\Http\Requests\Admin\Certification;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertificationRequest extends FormRequest
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
            'college_id' => 'required|exists:colleges,id',
            'major' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'valid_until' => 'required|date',
            'is_current' => 'required|boolean',
            'location' => 'string',
            'confirmation_link' => 'string',
        ];
    }
}
