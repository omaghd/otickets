<?php

namespace App\Models;

use App\Builders\CategoryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static CategoryBuilder query()
 */
class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:M d, Y h:i A',
    ];

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class)
            ->latest();
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class)
            ->latest();
    }

    public function cannedResponses(): HasMany
    {
        return $this->hasMany(CannedResponse::class)
            ->latest();
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function newEloquentBuilder($query): CategoryBuilder
    {
        return new CategoryBuilder($query);
    }
}
