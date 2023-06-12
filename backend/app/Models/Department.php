<?php

namespace App\Models;

use App\Builders\DepartmentBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static DepartmentBuilder query()
 */
class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:M d, Y h:i A',
    ];

    public function agents(): HasMany
    {
        return $this->hasMany(User::class, 'department_id')
            ->where('role', 'agent')
            ->latest();
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class)
            ->latest();
    }

    public function newEloquentBuilder($query): DepartmentBuilder
    {
        return new DepartmentBuilder($query);
    }
}
