<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class NotificationBuilder extends Builder
{
    public function __construct(QueryBuilder $query)
    {
        parent::__construct($query);
    }

    public function paginateOrGet(): array
    {
        return $this->when(
            request('paginate') == 'true',
            fn($q) => $q->paginate(10)->toArray(),
            fn($q) => $q->get()->toArray()
        );
    }
}
