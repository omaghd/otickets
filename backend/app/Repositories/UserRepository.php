<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepository implements UserRepositoryInterface
{
    public function getAdminsAndAgents(): array
    {
        return User::query()
            ->getTrashed()
            ->when(
                request('role') == 'agent',
                fn($q) => $q->getOnlyAgents()
            )
            ->filterByDepartment()
            ->filterByRole()
            ->filterByDates()
            ->getAdminsAndAgents()
            ->search()
            ->withDepartment()
            ->latest()
            ->paginateOrGet();
    }

    public function getClients(): array
    {
        return User::query()
            ->getTrashed()
            ->getOnlyClients()
            ->filterByDates()
            ->search()
            ->withTicketsCount()
            ->latest()
            ->paginateOrGet();
    }

    public function getRandomAgentByDepartment(int $departmentId): ?User
    {
        return User::query()
            ->getOnlyAgents()
            ->where('department_id', $departmentId)
            ->withCount('agentTickets')
            ->orderBy('agent_tickets_count')
            ->get()
            ->first();
    }

    public function countRelatedTicketsByAgentAndPriority(): array
    {
        return User::query()
            ->getAgentsWithTickets()
            ->withCount([
                'agentTickets as total_count',
                'agentTickets as high_count'   => fn($q) => $q->where('priority', 'high'),
                'agentTickets as medium_count' => fn($q) => $q->where('priority', 'medium'),
                'agentTickets as low_count'    => fn($q) => $q->where('priority', 'low'),
            ])
            ->latest()
            ->orderBy('total_count', 'desc')
            ->limit(5)
            ->get()
            ->map(fn(User $user) => [
                'name'   => $user->name,
                'high'   => $user->high_count,
                'medium' => $user->medium_count,
                'low'    => $user->low_count,
            ])
            ->toArray();
    }

    public function getAgentsWithTickets(): array
    {
        return User::query()
            ->getAgentsWithTickets()
            ->latest()
            ->get()
            ->toArray();
    }

    public function getCurrentAgentsWithTickets(): array
    {
        return User::query()
            ->getCurrentAgentTickets()
            ->latest()
            ->get()
            ->toArray();
    }

    public function getClientsStats(): array
    {
        return User::query()
            ->getOnlyClients()
            ->select(['created_at'])
            ->get()
            ->toArray();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = $this->find($id);
        $user->update($data);

        return $user;
    }

    public function find(int $id): User
    {
        try {
            return User::findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('User not found');
        }
    }

    public function findByEmail(string $email): User
    {
        try {
            return User::query()
                ->where('email', $email)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('User not found');
        }
    }

    public function delete(int $id): void
    {
        $user = $this->find($id);
        $user->delete();
    }

    public function restore(int $id): void
    {
        $user = $this->findInTrash($id);
        $user->restore();
    }

    public function findInTrash(int $id): User
    {
        try {
            return User::onlyTrashed()->findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('User not found');
        }
    }

    public function forceDelete(int $id): void
    {
        $user = $this->findInTrash($id);
        $user->forceDelete();
    }
}
