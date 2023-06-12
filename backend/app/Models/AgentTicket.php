<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

class AgentTicket extends Pivot
{
    protected $appends = ['transferred_by_user'];

    public function getTransferredByUserAttribute(): ?User
    {
        return $this->transferredByUser()->get()->first();
    }

    public function transferredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }

    public function getCreatedAtAttribute($value): string
    {
        return Carbon::parse($value)->format('M d, Y h:i A');
    }
}
