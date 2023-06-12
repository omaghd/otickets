<?php

namespace App\Models;

use App\Builders\NotificationBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Notifications\DatabaseNotification;

/**
 * @method static NotificationBuilder query()
 * @method Notification findOrFail(int $id)
 */
class Notification extends DatabaseNotification
{
    public function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => Carbon::parse($value)->diffForHumans(),
        );
    }

    public function newEloquentBuilder($query): NotificationBuilder
    {
        return new NotificationBuilder($query);
    }
}
