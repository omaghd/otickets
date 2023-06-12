<?php

namespace App\Services;

use App\Interfaces\TicketRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Jobs\StoreAttachmentsJob;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Notifications\AgentAssigned;
use App\Notifications\NewReply;
use App\Notifications\TicketClosed;
use App\Notifications\TicketCreated;
use App\Notifications\TicketResolved;
use Illuminate\Notifications\Notification;

class TicketService
{
    public function __construct(
        protected TicketRepositoryInterface $ticketRepository,
        protected UserRepositoryInterface   $userRepository,
    )
    {
    }

    public function getAllTickets(): array
    {
        return $this->ticketRepository->getAll();
    }

    public function getTicketByReference(string $reference): Ticket
    {
        return $this->ticketRepository->getByReference($reference);
    }

    public function countByStatus(): array
    {
        return $this->ticketRepository->getCountsByStatus();
    }

    public function getStats(): array
    {
        return $this->ticketRepository->getStats();
    }

    public function createTicket(array $data, ?int $agentId): Ticket
    {
        if (request()->user('sanctum')->isClient())
            $data['client_id'] = request()->user('sanctum')->id;

        $data['reference'] = $this->assignRandomReference();

        $ticket = $this->ticketRepository->create($data);

        $this->assignToAgent($ticket, $agentId);

        $this->notifyClient($ticket, new TicketCreated($ticket));
        $this->notifyAgent($ticket, new AgentAssigned($ticket));

        return $ticket;
    }

    public function assignRandomReference(): string
    {
        do $reference = generateTicketReference();
        while ($this->ticketRepository->isExisting($reference));

        return $reference;
    }

    public function assignToAgent(Ticket $ticket, int $agentId = null): void
    {
        if (!$agentId) {
            $agent = $this
                ->userRepository
                ->getRandomAgentByDepartment($ticket->category->department_id);

            if (!$agent) return;

            $agentId = $agent->id;
        }

        $transferredBy = auth('sanctum')->user()?->isClient()
            ? null
            : auth('sanctum')->user()?->id;
        $this->ticketRepository->attachAgent($ticket, $agentId, $transferredBy);

        $this->ticketRepository->update($ticket, ['status' => 'assigned']);
    }

    public function update(Ticket $ticket, array $data): Ticket
    {
        return $this->ticketRepository->update($ticket, $data);
    }

    private function notifyClient(Ticket $ticket, Notification $notification): void
    {
        $this->ticketRepository->getClient($ticket->id)->notify($notification);
    }

    public function notifyAgent(Ticket $ticket, Notification $notification): void
    {
        $this->ticketRepository->getAgent($ticket->id)?->notify($notification);
    }

    public function createReply(Ticket $ticket, array $data, string $action): TicketReply
    {
        $user = auth('sanctum')->user();

        $data['user_id']         = $user->id;
        $data['is_client_reply'] = $user->isClient();

        $ticketReply = $this->ticketRepository->createReply($ticket, $data);

        $this->handleAction($action, $ticket);

        if ($user->isClient())
            $this->notifyAgent($ticket, new NewReply($ticket));
        else
            $this->notifyClient($ticket, new NewReply($ticket));

        return $ticketReply;
    }

    private function handleAction(string $action, Ticket $ticket): void
    {
        if ($action === 'resolve') {
            $this->ticketRepository->update(
                $ticket,
                ['status' => 'resolved', 'resolved_at' => now()]
            );

            $this->notifyClient($ticket, new TicketResolved($ticket));
            $this->notifyAgent($ticket, new TicketResolved($ticket));
        } elseif ($action === 'close') {
            $this->ticketRepository->update(
                $ticket,
                ['status' => 'closed', 'closed_at' => now()]
            );

            $this->notifyClient($ticket, new TicketClosed($ticket));
            $this->notifyAgent($ticket, new TicketClosed($ticket));
        }
    }

    public function unassignAgent(Ticket $ticket, int $agentId): void
    {
        $this->ticketRepository->unassignCurrentAgent($ticket, $agentId);
    }

    public function assignAgent(Ticket $ticket, int $agentId): void
    {
        $this->ticketRepository->attachAgent(
            $ticket,
            $agentId,
            transferredById: auth('sanctum')->user()->id
        );

        if ($ticket->isUnassigned())
            $this->ticketRepository->update($ticket, ['status' => 'assigned']);
    }

    public function notifyAssignedAgent(Ticket $ticket, int $agentId): void
    {
        $agent = $this->userRepository->find($agentId);

        $agent->notify(new AgentAssigned($ticket));
    }

    public function find(int $id): Ticket
    {
        return $this->ticketRepository->find($id);
    }

    public function storeAttachments(Ticket|TicketReply $object, array $attachments): void
    {
        StoreAttachmentsJob::dispatch($object, $attachments)
            ->afterResponse();
    }

    public function findByReference(string $reference): Ticket
    {
        return $this->ticketRepository->findByReference($reference);
    }

    public function findInTrash(int $id): Ticket
    {
        return $this->ticketRepository->findInTrash($id);
    }

    public function delete(Ticket $ticket): void
    {
        $this->ticketRepository->delete($ticket);
    }

    public function restore(Ticket $ticket): void
    {
        $this->ticketRepository->restore($ticket);
    }

    public function forceDelete(Ticket $ticket): void
    {
        $this->ticketRepository->forceDelete($ticket);
    }
}
