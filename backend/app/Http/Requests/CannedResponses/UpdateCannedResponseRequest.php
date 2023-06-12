<?php

namespace App\Http\Requests\CannedResponses;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCannedResponseRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'title'       => 'sometimes|string|max:255',
            'content'     => 'sometimes|string',
            'category_id' => 'sometimes|integer|exists:categories,id',
        ];

        if (request()->user('sanctum')->isAdmin())
            $rules['agent_id'] = 'nullable|integer|exists:users,id';

        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }
}
