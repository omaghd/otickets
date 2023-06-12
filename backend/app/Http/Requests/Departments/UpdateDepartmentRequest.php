<?php

namespace App\Http\Requests\Departments;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|unique:departments,name,' . $this->department,
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
