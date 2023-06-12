<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'category_id' => 'sometimes|integer|exists:categories,id',
            'priority'    => 'sometimes|in:low,medium,high',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
