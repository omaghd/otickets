<?php

namespace App\Models;

use App\Builders\CannedResponseBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mews\Purifier\Casts\CleanHtml;

/**
 * @method static CannedResponseBuilder query()
 */
class CannedResponse extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'title'      => CleanHtml::class,
        'content'    => CleanHtml::class,
        'created_at' => 'datetime:M d, Y h:i A',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function newEloquentBuilder($query): CannedResponseBuilder
    {
        return new CannedResponseBuilder($query);
    }
}
