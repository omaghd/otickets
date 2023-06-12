<?php

namespace App\Http\Requests\Clients;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'phone'    => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'picture'  => 'sometimes|image|max:10000',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
