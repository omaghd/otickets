<?php

namespace App\Http\Requests\NewsLetter;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsLetterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'sometimes|email|unique:newsletters,email,' . $this->newsletter,
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
