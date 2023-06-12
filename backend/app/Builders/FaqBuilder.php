<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class FaqBuilder extends Builder
{
    public function __construct(QueryBuilder $query)
    {
        parent::__construct($query);
    }

    public function published(): self
    {
        return $this->where('is_published', true);
    }

    public function getTrashed(): self
    {
        return $this->when(
            request('trash') == 'true',
            fn($query) => $query->onlyTrashed()
        );
    }

    public function search(string $query = null): self
    {
        return $this->when(
            $query ?? request('query'),
            fn($q) => $q->where('question', 'like', '%' . request('query') . '%')
        );
    }

    public function filterByCategory(): self
    {
        return $this->when(
            request('category'),
            fn($q) => $q->where('category_id', request('category'))
        );
    }

    public function filterByDates(): self
    {
        return $this->when(
            request('dates'),
            fn($q) => $q->where(
                fn($q) => $q
                    ->whereDate('created_at', '>=', request('dates')[0])
                    ->whereDate('created_at', '<=', request('dates')[1])
            )
        );
    }

    public function getBySlug($slug): self
    {
        return $this->when(
            $slug,
            fn($q) => $q->where('slug', $slug)
        );
    }

    public function withCategory(): self
    {
        return $this->with('category');
    }

    public function withFaqs(): self
    {
        return $this->with('category.faqs');
    }
}
