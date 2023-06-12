<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Ticket $ticket): bool
    {
        return $this->isCurrentClient($user, $ticket)
            || $this->isAmongAgents($user, $ticket)
            || $user->isAdmin();
    }

    private function isCurrentClient(User $user, Ticket $ticket): bool
    {
        return $user->isClient()
            && $ticket->client_id === $user->id;
    }

    private function isAmongAgents(User $user, Ticket $ticket): bool
    {
        return $user->isAgent()
            && $ticket->agents->contains($user);
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin()
            || $this->isCurrentAgent($user, $ticket);
    }

    private function isCurrentAgent(User $user, Ticket $ticket): bool
    {
        return $user->isAgent()
            && $ticket->agents->contains($user)
            && $ticket->agents->where('id', $user->id)->first()->pivot->is_current;
    }

    public function markAsResolvedOrClosed(User $user, Ticket $ticket): bool
    {
        return $ticket->isNotResolved()
            && (
                $user->isAdmin()
                || $this->isCurrentAgent($user, $ticket)
                || $this->isCurrentClient($user, $ticket)
            );
    }

    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user): bool
    {
        return $user->isAdmin();
    }

    public function assignAgent(User $user, Ticket $ticket): bool
    {
        return $ticket->isNotResolved()
            && ($user->isAdmin() || $this->isCurrentAgent($user, $ticket));
    }

    public function assignToMe(User $user, Ticket $ticket): bool
    {
        return $ticket->isNotResolved()
            && $user->isAgent()
            && $ticket->isUnassigned();
    }
}
