<?php

namespace App\Interfaces;

use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;

interface TicketRepositoryInterface
{
    public function getAll(): array;

    public function getCountsByStatus(): array;

    public function getStats(): array;

    public function getByReference(string $reference): Ticket;

    public function getAgent(int $ticketId): ?User;

    public function getClient(int $ticketId): User;

    public function find(int $id): Ticket;

    public function findInTrash(int $id): Ticket;

    public function findByReference(string $reference): Ticket;

    public function isExisting(string $reference): bool;

    public function unassignCurrentAgent(Ticket $ticket, int $currentAgentId): void;

    public function attachAgent(Ticket $ticket, int $agentId, int $transferredById): void;

    public function create(array $data): Ticket;

    public function createReply(Ticket $ticket, array $data): TicketReply;

    public function createAttachment(Ticket|TicketReply $object, array $attachments): void;

    public function update(Ticket $ticket, array $data): Ticket;

    public function delete(Ticket $ticket): void;

    public function restore(Ticket $ticket): void;

    public function forceDelete(Ticket $ticket): void;
}
