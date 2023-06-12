<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Jobs\SaveProfilePictureJob;
use App\Models\User;
use App\Notifications\PasswordChanged;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(protected UserRepositoryInterface $repository)
    {
    }

    public function getAdminsAndAgents(): array
    {
        return $this->repository->getAdminsAndAgents();
    }

    public function getClients(): array
    {
        return $this->repository->getClients();
    }

    public function countRelatedTicketsByAgentAndPriority(): array
    {
        return $this->repository->countRelatedTicketsByAgentAndPriority();
    }

    public function getClientsStats(): array
    {
        return $this->repository->getClientsStats();
    }

    public function getAgentsResponseTime(): array
    {
        $agents        = $this->repository->getCurrentAgentsWithTickets();
        $responseTimes = [];

        foreach ($agents as $agent) {
            if (!array_key_exists("agent_tickets", $agent)) continue;

            $resolvedTickets = array_filter(
                $agent['agent_tickets'],
                fn($ticket) => $ticket['status'] === 'resolved'
            );

            if (empty($resolvedTickets)) continue;

            $totalResponseTime = array_reduce(
                $resolvedTickets,
                fn($carry, $ticket) => $carry + Carbon::make($ticket['resolved_at'])
                        ->diffInHours(Carbon::make($ticket['created_at'])),
                0
            );

            $averageResponseTime = $totalResponseTime / count($resolvedTickets);

            $responseTimes[] = [
                'name'  => $agent['name'],
                'value' => $averageResponseTime,
            ];
        }

        return $responseTimes;
    }


    public function saveProfilePicture(User $user, UploadedFile $picture): void
    {
        SaveProfilePictureJob::dispatch($user, $picture)
            ->afterResponse();
    }

    public function find(int $id): User
    {
        return $this->repository->find($id);
    }

    public function restore(int $id): void
    {
        $this->repository->restore($id);
    }

    public function forceDelete(int $id): void
    {
        $this->repository->forceDelete($id);
    }

    public function updateProfile(array $data): User
    {
        $user = auth('sanctum')->user();
        return $this->repository->update($user->id, $data);
    }

    public function update(int $id, array $data): User
    {
        return $this->repository->update($id, $data);
    }

    /**
     * @throws AuthenticationException
     */
    public function updatePassword(array $data): void
    {
        $user = auth('sanctum')->user();

        if (!Auth::guard('web')
            ->attempt(['email' => $user->email, 'password' => $data['current_password']])
        ) {
            throw new AuthenticationException('Current password is incorrect');
        }

        $user = $this->repository->update($user->id, ['password' => $data['password']]);

        $user->notify(new PasswordChanged());
    }

    public function register(array $data): array
    {
        $user = $this->create($data, true);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    public function create(array $data, $isClient = false): User
    {
        if ($isClient) $data['role'] = 'client';

        return $this->repository->create($data);
    }

    /**
     * @throws AuthenticationException
     */
    public function login(array $credentials): array
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->isAgent()) $user->load(['department']);

            return [
                'user'  => $user,
                'token' => $user->createToken('auth_token')->plainTextToken,
            ];
        }

        throw new AuthenticationException('Invalid credentials');
    }

    /**
     * @throws Exception
     */
    public function forgotPassword(array $credentials): void
    {
        $status = Password::sendResetLink($credentials);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new Exception('Unable to send reset link');
        }
    }

    public function resetPassword(array $data): void
    {
        $status = Password::reset($data, function ($user, $password) {
            $this->repository->update($user, [
                'password'       => $password,
                'remember_token' => Str::random(10),
            ]);

            event(new PasswordReset($user));

            $user->notify(new PasswordChanged);
        });

        if ($status != Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'message' => 'Password reset failed'
            ]);
        }
    }

    public function logout(): void
    {
        Auth::user()->currentAccessToken()->delete();
    }

    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }
}
