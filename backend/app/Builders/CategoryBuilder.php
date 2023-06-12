<?php

namespace App\Builders;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @method Category firstOrFail()
 */
class CategoryBuilder extends Builder
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
            fn($q) => $q->where('name', 'like', '%' . request('query') . '%')
        );
    }

    public function filterByDepartment(): self
    {
        return $this->when(
            request('department'),
            fn($q) => $q->where('department_id', request('department'))
        );
    }

    public function getBySlug(string $slug): self
    {
        return $this->where('slug', $slug);
    }

    public function withDepartment(): self
    {
        return $this->with('department');
    }

    public function withFaqs(): self
    {
        return $this->with([
            'faqs' => fn($query) => $query->published()
        ]);
    }

    public function whereHasTickets(): self
    {
        return $this->whereHas('tickets', fn($query) => $query->getOwn());
    }

    public function withTicketsCount(): self
    {
        return $this->withCount([
            'tickets' => fn($query) => $query->getOwn()
        ]);
    }

    public function withCannedResponsesCount(): self
    {
        return $this->withCount('cannedResponses');
    }

    public function withFaqsCount($onlyPublished = false): self
    {
        return $this->when(
            $onlyPublished,
            fn($q) => $q->withCount(['faqs' => fn($q) => $q->where('is_published', true)]),
            fn($q) => $q->withCount('faqs')
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
