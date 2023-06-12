<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {

        $validation = [
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|string|email|max:255|unique:users,email,' . $this->user,
            'phone'    => 'sometimes|string|max:255',
            'role'     => 'sometimes|in:admin,agent',
            'password' => 'nullable|string|min:8',
            'picture'  => 'sometimes|image|max:10000',
        ];

        if ($this->role === 'agent')
            $validation['department_id'] = 'required|exists:departments,id';

        return $validation;
    }

    public function authorize(): bool
    {
        return true;
    }
}
