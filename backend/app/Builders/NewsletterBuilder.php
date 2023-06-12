<?php

namespace App\Builders;

use App\Models\Newsletter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @method Newsletter findOrFail(int $id)
 */
class NewsletterBuilder extends Builder
{
    public function __construct(QueryBuilder $query)
    {
        parent::__construct($query);
    }

    public function search(): self
    {
        return $this->when(
            request('query'),
            fn($query) => $query->where(
                fn($query) => $query
                    ->where('email', 'like', '%' . request('query') . '%')
                    ->orWhere('ip_address', 'like', '%' . request('query') . '%')
                    ->orWhere('user_agent', 'like', '%' . request('query') . '%')
            )
        );
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
