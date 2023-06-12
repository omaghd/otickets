<?php

namespace App\Builders;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @method Ticket firstOrFail()
 */
class TicketBuilder extends Builder
{
    public function __construct(QueryBuilder $query)
    {
        parent::__construct($query);
    }

    public function getTrashed(): self
    {
        return $this
            ->when(
                request('trash') == 'true' && request()->user('sanctum')->isAdmin(),
                fn($q) => $q->onlyTrashed()
            );
    }

    public function countByStatus(string $status): int
    {
        return $this
            ->when(
                request()->user('sanctum')->isAdmin(),
                fn($q) => $q->where('status', $status)
            )
            ->when(
                request()->user('sanctum')->isClient(),
                fn($q) => $q->where(
                    fn($q) => $q
                        ->where('client_id', request()->user('sanctum')->id)
                        ->where('status', $status)
                )
            )
            ->when(
                request()->user('sanctum')->isAgent(),
                fn($q) => $q
                    ->when(
                        $status == 'unassigned',
                        fn($q) => $q
                            ->whereHas(
                                'category',
                                fn($q) => $q
                                    ->where(fn($q) => $q
                                        ->where('department_id', request()->user('sanctum')->department_id)
                                        ->where('status', $status)
                                    )
                            ),
                        fn($q) => $q
                            ->whereHas(
                                'agents',
                                fn($q) => $q->where(fn($q) => $q
                                    ->where('agent_id', request()->user('sanctum')->id)
                                    ->where('status', $status)
                                )
                            )
                    )
            )
            ->count();
    }

    public function getOwn(): self
    {
        return $this
            ->when(
                request()->user('sanctum')->isClient(),
                fn($q) => $q->where('client_id', request()->user('sanctum')->id)
            )
            ->when(
                request()->user('sanctum')->isAgent(),
                fn($q) => $q
                    ->whereHas(
                        'agents',
                        fn($q) => $q->where('agent_id', request()->user('sanctum')->id)
                    )
                    ->when(
                        request('status') == 'unassigned',
                        fn($q) => $q
                            ->orWhereHas(
                                'category',
                                fn($q) => $q
                                    ->where(fn($q) => $q
                                        ->where('status', 'unassigned')
                                        ->where('department_id', request()->user('sanctum')->department_id)
                                    )
                            )
                    )
            );
    }

    public function getCurrentAgent($ticketId): ?User
    {
        $currentAgent = $this
            ->whereHas(
                'agents',
                fn($q) => $q->where(fn($q) => $q
                    ->where('ticket_id', $ticketId)
                    ->where('is_current', true)
                )
            )
            ->with('agents')
            ->get()
            ->first()
            ?->agents()
            ->get()
            ->first();

        return $currentAgent;
    }

    public function getByReference(string $reference): self
    {
        return $this->where('reference', $reference);
    }

    public function filterByQuery(): self
    {
        return $this
            ->when(
                request('query'),
                fn($q) => $q->where(
                    fn($q) => $q
                        ->where('subject', 'like', '%' . request('query') . '%')
                        ->orWhere('reference', 'like', '%' . request('query') . '%')
                )
            );
    }

    public function filterByPriority(): self
    {
        return $this
            ->when(
                request('priority'),
                fn($q) => $q->where('priority', request('priority'))
            );
    }

    public function filterByAgent(): self
    {
        return $this
            ->when(
                request('agent') && request()->user('sanctum')->isAdmin(),
                fn($q) => $q->whereHas(
                    'agents',
                    fn($q) => $q->where('agent_id', request('agent'))
                )
            );
    }

    public function filterByClient(): self
    {
        return $this
            ->when(
                request('client'),
                fn($q) => $q->where('client_id', request('client'))
            );
    }

    public function filterByStatus(): self
    {
        return $this
            ->when(
                request('status') == 'unassigned' && request()->user('sanctum')->isAgent(),
                fn($q) => $q
                    ->where(fn($q) => $q
                        ->where(fn($q) => $q
                            ->where('status', 'unassigned')
                            ->whereHas(
                                'category',
                                fn($q) => $q
                                    ->where('department_id', request()->user('sanctum')->department_id)
                            )
                        )
                    ),
                fn($q) => $q->when(request('status'), fn($q) => $q->where('status', request('status')))
            );
    }

    public function filterByCategory(): self
    {
        return $this
            ->when(
                request('category'),
                fn($q) => $q->where('category_id', request('category'))
            );
    }

    public function filterByDates(): self
    {
        $dates = request('dates');
        return $this
            ->when(
                $dates,
                fn($q) => $q->where(
                    fn($q) => $q
                        ->whereDate('created_at', '>=', $dates[0])
                        ->whereDate('created_at', '<=', $dates[1])
                )
            );
    }

    public function withReplies(): self
    {
        return $this->with('replies.user.department');
    }

    public function withAttachments(): self
    {
        return $this->with('attachments');
    }

    public function withRepliesAttachments(): self
    {
        return $this->with('replies.attachments');
    }

    public function withAgents(): self
    {
        return $this->with(['agents.department']);
    }

    public function withCategory(): self
    {
        return $this->with('category');
    }

    public function withDepartment(): self
    {
        return $this->with('category.department');
    }

    public function withClient(): self
    {
        return $this->with('client');
    }

    public function withRepliesCount(): self
    {
        return $this->withCount('replies');
    }

    public function withAgentsCount(): self
    {
        return $this->withCount('agents');
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
