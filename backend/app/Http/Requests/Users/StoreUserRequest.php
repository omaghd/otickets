<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        $validation = [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'phone'    => 'required|string|max:255',
            'role'     => 'required|in:admin,agent',
            'password' => 'required|string|min:8',
            'picture'  => 'sometimes|image|max:10000',
        ];

        if ($this->role === 'agent')
            $validation['department_id'] = 'required|exists:departments,id';

        return $validation;
    }

    public function messages(): array
    {
        return [
            'department_id.required' => 'The department field is required.',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
