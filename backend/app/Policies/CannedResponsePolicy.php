<?php

namespace App\Policies;

use App\Models\CannedResponse;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CannedResponsePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isManager();
    }

    public function create(User $user): bool
    {
        return $user->isManager();
    }

    public function update(User $user, CannedResponse $cannedResponse): bool
    {
        return $user->isAdmin() || $cannedResponse->agent_id === $user->id;
    }

    public function delete(User $user, CannedResponse $cannedResponse): bool
    {
        return $user->isAdmin() || $cannedResponse->agent_id === $user->id;
    }

    public function restore(User $user, CannedResponse $cannedResponse): bool
    {
        return $user->isAdmin() || $cannedResponse->agent_id === $user->id;
    }

    public function forceDelete(User $user, CannedResponse $cannedResponse): bool
    {
        return $user->isAdmin() || $cannedResponse->agent_id === $user->id;
    }
}
