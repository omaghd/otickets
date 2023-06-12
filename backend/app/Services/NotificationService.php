<?php

namespace App\Services;

use App\Interfaces\NotificationRepositoryInterface;
use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function __construct(protected NotificationRepositoryInterface $repository)
    {
    }

    public function getAll(User $user): array
    {
        return $this->repository->getAll($user);
    }

    public function find(string $id): Notification
    {
        return $this->repository->find($id);
    }

    public function getUnread(User $user): array
    {
        return $this->repository->getUnread($user);
    }

    public function getCounts(User $user): array
    {
        return $this->repository->getCounts($user);
    }

    public function markAllAsRead(User $user): void
    {
        $this->repository->markAllAsRead($user);
    }

    public function markAsRead(Notification $notification): void
    {
        $this->repository->markAsRead($notification);
    }

    public function delete(Notification $notification): void
    {
        $this->repository->delete($notification);
    }
}
