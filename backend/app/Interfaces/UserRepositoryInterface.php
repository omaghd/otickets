<?php

namespace App\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public function getAdminsAndAgents(): array;

    public function getClients(): array;

    public function getRandomAgentByDepartment(int $departmentId): ?User;

    public function countRelatedTicketsByAgentAndPriority(): array;

    public function getClientsStats(): array;

    public function getAgentsWithTickets(): array;

    public function getCurrentAgentsWithTickets(): array;

    public function findByEmail(string $email): User;

    public function find(int $id): User;

    public function findInTrash(int $id): User;

    public function create(array $data): User;

    public function update(int $id, array $data): User;

    public function delete(int $id): void;

    public function restore(int $id): void;

    public function forceDelete(int $id): void;
}
