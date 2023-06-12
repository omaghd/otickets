<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'subject'       => 'required|string|max:255',
            'priority'      => 'required|in:low,medium,high',
            'description'   => 'required|string|min:10',
            'category_id'   => 'required|integer|exists:categories,id',
            'attachments.*' => 'nullable|image|max:10000',
        ];

        if (request()->user('sanctum')->isManager()) {
            $rules['client_id'] = 'required|integer|exists:users,id,role,client';
            $rules['agent_id']  = 'nullable|integer|exists:users,id,role,agent';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'The category field is required',
            'attachments.*.image'  => 'The file :position must be an image',
            'attachments.*.max'    => 'The image :position must be less than :max kilobytes',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
