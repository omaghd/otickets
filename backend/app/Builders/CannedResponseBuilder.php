<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class CannedResponseBuilder extends Builder
{
    public function __construct(QueryBuilder $query)
    {
        parent::__construct($query);
    }

    public function getTrashed(): self
    {
        return $this->when(
            request('trash') == 'true',
            fn($query) => $query->onlyTrashed()
        );
    }

    public function getOwn(): self
    {
        return $this->when(
            request()->user('sanctum')->isAgent(),
            fn($query) => $query
                ->where(fn($query) => $query
                    ->where('agent_id', request()->user('sanctum')->id)
                    ->orWhere(fn($query) => $query
                        ->whereNull('agent_id')
                        ->whereHas(
                            'category',
                            fn($query) => $query->where(
                                'department_id',
                                request()->user('sanctum')->department_id
                            )
                        )
                    )
                )
        );
    }

    public function search(): self
    {
        return $this->when(
            request('query'),
            fn($query) => $query->where(
                fn($query) => $query
                    ->where('title', 'like', '%' . request('query') . '%')
                    ->orWhere('content', 'like', '%' . request('query') . '%')
            )
        );
    }

    public function filterByCategory(): self
    {
        return $this->when(
            request('category'),
            fn($query) => $query->where('category_id', request('category'))
        );
    }

    public function filterByAgent(): self
    {
        return $this->when(
            request('agent') && request()->user('sanctum')->isAdmin(),
            fn($query) => $query->where('agent_id', request('agent'))
        );
    }

    public function withCategory(): self
    {
        return $this->when(
            request()->user('sanctum')->isManager(),
            fn($query) => $query->with(['category'])
        );
    }

    public function withAgent(): self
    {
        return $this->when(
            request()->user('sanctum')->isManager(),
            fn($query) => $query->with(['agent'])
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
