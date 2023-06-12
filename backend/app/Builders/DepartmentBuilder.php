<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class DepartmentBuilder extends Builder
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

    public function search(): self
    {
        return $this->when(
            request('query'),
            fn($query) => $query->where('name', 'like', '%' . request('query') . '%')
        );
    }

    public function withAgents(): self
    {
        return $this->with('agents');
    }

    public function withCategories(): self
    {
        return $this->with('categories');
    }

    public function withAgentsCount(): self
    {
        return $this->withCount('agents');
    }

    public function withCategoriesCount(): self
    {
        return $this->withCount('categories');
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
