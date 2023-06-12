<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class UserBuilder extends Builder
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

    public function getAgentsWithTickets(): self
    {
        return $this->getOnlyAgents()
            ->whereHas('agentTickets')
            ->with('agentTickets');
    }

    public function getCurrentAgentTickets(): self
    {
        return $this->getOnlyAgents()
            ->whereHas('agentTickets', fn($q) => $q->where('is_current', true))
            ->with('agentTickets');
    }

    public function getOnlyAgents(): self
    {
        return $this->where('role', 'agent');
    }

    public function getOnlyClients(): self
    {
        return $this->where('role', 'client');
    }

    public function getAdminsAndAgents(): self
    {
        return $this->whereIn('role', ['admin', 'agent']);
    }

    public function search(): self
    {
        return $this->when(
            request('query'),
            fn($q) => $q->where(
                fn($q) => $q
                    ->where('name', 'like', '%' . request('query') . '%')
                    ->orWhere('email', 'like', '%' . request('query') . '%')
            )
        );
    }

    public function filterByDepartment(): self
    {
        return $this->when(
            request('department'),
            fn($q) => $q->where('department_id', request('department'))
        );
    }

    public function filterByRole(): self
    {
        return $this->when(
            request('role'),
            fn($q) => $q->where('role', request('role'))
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

    public function withDepartment(): self
    {
        return $this->with('department');
    }

    public function withTicketsCount(): self
    {
        return $this->withCount('tickets');
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
