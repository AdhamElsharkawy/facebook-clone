<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user)],
            'password' => ['string', 'confirmed'],
            'role' => ['required', 'in:user,admin,manager,team_leader'],
            'status' => ['required', 'in:active,blocked'],
            'mobile' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'team_score' => ['required', 'integer'],
            'global_score' => ['required', 'integer'],
            'department_id' => ['required', 'exists:departments,id'],

        ];
    }
}
