<?php

namespace App\Http\Requests\Faqs;

use Illuminate\Foundation\Http\FormRequest;

class StoreFaqRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'question'     => 'required|string|max:255',
            'slug'         => 'required|string|max:255|unique:faqs,slug',
            'answer'       => 'required|string',
            'excerpt'      => 'required|string|max:255',
            'is_published' => 'sometimes|boolean',
            'category_id'  => 'required|integer|exists:categories,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
