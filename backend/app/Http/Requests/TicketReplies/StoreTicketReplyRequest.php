<?php

namespace App\Http\Requests\TicketReplies;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketReplyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'action'        => 'required|string|in:reply,resolve,close',
            'ticket_id'     => 'required|integer|exists:tickets,id',
            'content'       => 'required|string',
            'attachments.*' => 'nullable|image|max:10000',
        ];
    }

    public function messages(): array
    {
        return [
            'attachments.*.image' => 'The file :position must be an image',
            'attachments.*.max'   => 'The image :position must be less than 10MB',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
