<?php

namespace App\Http\Requests\Categories;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255|unique:categories,name',
            'slug'          => 'required|string|max:255|unique:categories,slug',
            'department_id' => 'required|integer|exists:departments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'department_id.required' => 'The department field is required',
            'department_id.integer'  => 'The department field must be an integer',
            'department_id.exists'   => 'The department field does not exist',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
