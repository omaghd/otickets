<?php

namespace App\Http\Requests\Categories;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'          => 'sometimes|string|max:255|unique:categories,name,' . $this->category,
            'slug'          => 'sometimes|string|max:255|unique:categories,slug,' . $this->category,
            'department_id' => 'sometimes|integer|exists:departments,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
