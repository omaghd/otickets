<?php

namespace App\Models;

use App\Builders\TicketBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mews\Purifier\Casts\CleanHtml;

/**
 * @method static TicketBuilder query()
 */
class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'subject'     => CleanHtml::class,
        'description' => CleanHtml::class,
        'created_at'  => 'datetime:M d, Y',
        'resolved_at' => 'datetime:M d, Y',
    ];

    public function currentAgent(): ?User
    {
        return $this->agents()
            ->wherePivot('is_current', true)
            ->get()
            ->first();
    }

    public function agents(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                User::class,
                'agents_tickets',
                'ticket_id',
                'agent_id'
            )
            ->using(AgentTicket::class)
            ->withPivot('is_current', 'transferred_by')
            ->latest('agents_tickets.created_at')
            ->withTimestamps();
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class)
            ->latest();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class)
            ->latest();
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isNotResolved(): bool
    {
        return !$this->isResolved() && $this->status !== 'closed';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isUnassigned(): bool
    {
        return $this->status === 'unassigned';
    }

    public function newEloquentBuilder($query): TicketBuilder
    {
        return new TicketBuilder($query);
    }
}
