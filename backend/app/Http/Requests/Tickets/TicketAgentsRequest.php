<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;

class TicketAgentsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ticket_id'   => 'required',
            'agent_id'    => 'required|exists:users,id,role,agent',
            'transfer_to' => 'required|in:me,agent'
        ];
    }

    public function messages(): array
    {
        return [
            'agent_id.required' => 'The agent field is required'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
