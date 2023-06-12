<?php

namespace App\Http\Requests\NewsLetter;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsLetterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:newsletters,email',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
