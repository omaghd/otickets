<?php

namespace App\Http\Requests\Clients;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|string|email|max:255|unique:users,email,' . $this->client,
            'phone'    => 'sometimes|string|max:255',
            'picture'  => 'sometimes|image|max:10000',
            'password' => 'nullable|string|min:8',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
