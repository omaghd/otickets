<?php

namespace App\Http\Requests\Departments;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:departments,name',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
