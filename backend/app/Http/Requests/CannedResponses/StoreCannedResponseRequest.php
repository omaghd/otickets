<?php

namespace App\Http\Requests\CannedResponses;

use Illuminate\Foundation\Http\FormRequest;

class StoreCannedResponseRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
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
