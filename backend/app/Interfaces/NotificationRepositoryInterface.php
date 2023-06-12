<?php

namespace App\Interfaces;

use App\Models\Notification;
use App\Models\User;

interface NotificationRepositoryInterface
{
    public function getAll(User $user): array;

    public function getUnread(User $user): array;

    public function markAsRead(Notification $notification): void;

    public function markAllAsRead(User $user): void;

    public function find(string $id): Notification;

    public function delete(Notification $notification): void;

    public function getCounts(User $user): array;
}
