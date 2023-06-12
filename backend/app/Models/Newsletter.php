<?php

namespace App\Models;

use App\Builders\NewsletterBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static NewsletterBuilder query()
 * @method restore()
 */
class Newsletter extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:M d, Y h:i A',
    ];

    public function newEloquentBuilder($query): NewsletterBuilder
    {
        return new NewsletterBuilder($query);
    }
}
