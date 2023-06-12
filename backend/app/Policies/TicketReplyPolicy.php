<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketReplyPolicy
{
    use HandlesAuthorization;

    public function create(User $user, Ticket $ticket): bool
    {
        return $ticket->isNotResolved()
            && ($this->isCurrentAgent($user, $ticket) || $this->isCurrentClient($user, $ticket));
    }

    private function isCurrentAgent(User $user, Ticket $ticket): bool
    {
        return $user->isAgent()
            && $ticket->agents->contains($user)
            && $ticket->agents->where('id', $user->id)->first()->pivot->is_current;
    }

    private function isCurrentClient(User $user, Ticket $ticket): bool
    {
        return $user->isClient()
            && $ticket->client_id === $user->id;
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
}
