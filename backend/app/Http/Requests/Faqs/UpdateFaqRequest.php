<?php

namespace App\Http\Requests\Faqs;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFaqRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'question'     => 'sometimes|string|max:255',
            'slug'         => 'sometimes|string|max:255|unique:faqs,slug,' . $this->faq,
            'answer'       => 'sometimes|string',
            'excerpt'      => 'sometimes|string|max:255',
            'is_published' => 'sometimes|boolean',
            'category_id'  => 'sometimes|integer|exists:categories,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
