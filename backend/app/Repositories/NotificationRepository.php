<?php

namespace App\Repositories;

use App\Interfaces\NotificationRepositoryInterface;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function delete(Notification $notification): void
    {
        $notification->delete();
    }

    public function find(string $id): Notification
    {
        try {
            return Notification::findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('Notification not found');
        }
    }

    public function getAll(User $user): array
    {
        return $user
            ->notifications()
            ->paginate(10)
            ->toArray();
    }

    public function getUnread(User $user): array
    {
        return $user
            ->unreadNotifications()
            ->paginate(10)
            ->toArray();
    }

    public function markAllAsRead(User $user): void
    {
        $user->unreadNotifications->markAsRead();
    }

    public function markAsRead(Notification $notification): void
    {
        $notification->markAsRead();
    }

    public function getCounts(User $user): array
    {
        return [
            'total'  => $user->notifications()->count(),
            'unread' => $user->unreadNotifications()->count(),
            'read'   => $user->readNotifications()->count(),
        ];
    }
}
