<?php

namespace App\Repositories;

use App\Interfaces\TicketRepositoryInterface;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketRepository implements TicketRepositoryInterface
{
    public function getAll(): array
    {
        return Ticket::query()
            ->getTrashed()
            ->filterByClient()
            ->filterByAgent()
            ->filterByQuery()
            ->filterByPriority()
            ->filterByStatus()
            ->filterByCategory()
            ->filterByDates()
            ->getOwn()
            ->withReplies()
            ->withAgents()
            ->withCategory()
            ->withDepartment()
            ->withClient()
            ->withRepliesCount()
            ->withAgentsCount()
            ->latest()
            ->paginate(10)
            ->toArray();
    }

    public function getClient(int $ticketId): User
    {
        return Ticket::query()
            ->findOrFail($ticketId)
            ->client;
    }

    public function getAgent(int $ticketId): ?User
    {
        return Ticket::query()
            ->getCurrentAgent($ticketId);
    }

    public function find(int $id): Ticket
    {
        try {
            return Ticket::findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('Ticket not found');
        }
    }

    public function getCountsByStatus(): array
    {
        return [
            [
                'name'  => 'Total Tickets',
                'count' => Ticket::query()->getOwn()->count()
            ],
            [
                'name'  => 'Assigned Tickets',
                'count' => Ticket::query()->countByStatus('assigned')
            ],
            [
                'name'  => 'Unassigned Tickets',
                'count' => Ticket::query()->countByStatus('unassigned')
            ],
            [
                'name'  => 'Resolved Tickets',
                'count' => Ticket::query()->countByStatus('resolved')
            ],
            [
                'name'  => 'Closed Tickets',
                'count' => Ticket::query()->countByStatus('closed')
            ],
        ];
    }

    public function getStats(): array
    {
        return Ticket::query()
            ->getOwn()
            ->select(['created_at'])
            ->get()
            ->map(fn(Ticket $ticket) => ['created_at' => $ticket->getOriginal('created_at')])
            ->toArray();
    }

    public function unassignCurrentAgent(Ticket $ticket, int $currentAgentId): void
    {
        $ticket
            ->agents()
            ->each(function ($agent) use ($currentAgentId) {
                if ($agent->id == $currentAgentId) {
                    $agent->pivot->is_current = false;
                    $agent->pivot->save();
                }
            });
    }

    public function attachAgent(Ticket $ticket, int $agentId, ?int $transferredById): void
    {
        $ticket
            ->agents()
            ->attach(
                $agentId,
                [
                    'is_current'     => true,
                    'transferred_by' => $transferredById
                ]
            );
    }

    public function createAttachment(Ticket|TicketReply $object, array $attachments): void
    {
        $object->attachments()->create($attachments);
    }

    public function create(array $data): Ticket
    {
        return Ticket::create($data);
    }

    public function createReply(Ticket $ticket, array $data): TicketReply
    {
        return $ticket->replies()->create($data);
    }

    public function isExisting(string $reference): bool
    {
        return Ticket::query()
            ->getByReference($reference)
            ->exists();
    }

    public function getByReference(string $reference): Ticket
    {
        try {
            return Ticket::query()
                ->getByReference($reference)
                ->withReplies()
                ->withAgents()
                ->withCategory()
                ->withDepartment()
                ->withClient()
                ->withAttachments()
                ->withRepliesAttachments()
                ->withRepliesCount()
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('Ticket not found');
        }
    }

    public function update(Ticket $ticket, array $data): Ticket
    {
        $ticket->update($data);

        return $ticket;
    }

    public function findByReference(string $reference): Ticket
    {
        try {
            return Ticket::query()
                ->getByReference($reference)
                ->get()
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('Ticket not found');
        }
    }

    public function delete(Ticket $ticket): void
    {
        $ticket->delete();
    }

    public function restore(Ticket $ticket): void
    {
        $ticket->restore();
    }

    public function findInTrash(int $id): Ticket
    {
        try {
            return Ticket::onlyTrashed()->findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('Ticket not found');
        }
    }

    public function forceDelete(Ticket $ticket): void
    {
        $ticket->forceDelete();
    }
}
