<?php

namespace App\Models;

use App\Builders\UserBuilder;
use App\Notifications\PasswordReset;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method static UserBuilder query()
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at'        => 'datetime:M d, Y h:i A',
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'client_id')
            ->latest();
    }

    public function agentTickets(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Ticket::class,
                'agents_tickets',
                'agent_id',
                'ticket_id'
            )
            ->withPivot('is_current', 'transferred_by')
            ->latest('agents_tickets.created_at')
            ->withTimestamps();
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable')
            ->orderBy('created_at', 'desc');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordReset($token));
    }

    public function password(): Attribute
    {
        return Attribute::make(set: fn($value) => bcrypt($value));
    }

    public function isManager(): bool
    {
        return $this->isAdmin() || $this->isAgent();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function newEloquentBuilder($query): UserBuilder
    {
        return new UserBuilder($query);
    }
}
