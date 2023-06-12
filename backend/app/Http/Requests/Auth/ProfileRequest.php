<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'    => 'sometimes|string|max:255',
            'phone'   => 'sometimes|string|max:255',
            'email'   => 'sometimes|string|email|max:255|unique:users,email,' . auth()->id(),
            'picture' => 'nullable|image|max:10000',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
